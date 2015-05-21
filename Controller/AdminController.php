<?php

namespace Wizin\Bundle\SimpleCmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Wizin\Bundle\SimpleCmsBundle\Entity\Content;
use Wizin\Bundle\SimpleCmsBundle\Form\ContentType;
use Wizin\Bundle\SimpleCmsBundle\Traits\ControllerTrait;

/**
 * Class AdminController
 * @package Wizin\Bundle\SimpleCmsBundle\Controller
 */
class AdminController extends Controller
{
    /**
     * \Wizin\Bundle\SimpleCmsBundle\Traits\ControllerTrait
     */
    use ControllerTrait;

    /**
     * @Route("/", name="wizin_simple_cms_admin_index")
     * @Template()
     */
    public function indexAction()
    {
        return $this->forward('WizinSimpleCmsBundle:Admin:list');
    }

    /**
     * @Route("/list", name="wizin_simple_cms_admin_list")
     * @Template()
     */
    public function listAction()
    {
        $contents = $this->getContentRepository()->findAll();
        $baseUrl = $this->getBaseUrl();

        return ['contents' => $contents, 'baseUrl' => $baseUrl];
    }

    /**
     * @Route(
     *   "/add/{templateFile}",
     *   name="wizin_simple_cms_admin_add",
     *   requirements={"templateFile"=".*"},
     *   defaults={"templateFile"=""}
     * )
     * @Template()
     */
    public function addAction($templateFile)
    {
        if ($templateFile === '') {
            return $this->forward('WizinSimpleCmsBundle:Admin:selectTemplateFile');
        }
        $content = new Content();
        $form = $this->createContentForm($content, $templateFile);
        if ($this->getRequest()->isMethod('POST')) {
            $form->handleRequest($this->getRequest());
            if ($form->isValid()) {
                // persist entity
                $this->getEntityManager()->persist($content);
                $this->getEntityManager()->flush();

                return $this->redirect($this->generateUrl('wizin_simple_cms_admin_index'));
            }
        }

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/selectTemplateFile", name="wizin_simple_cms_admin_select_template_file")
     * @Template("WizinSimpleCmsBundle:Admin:select_template_file.html.twig")
     */
    public function selectTemplateFileAction()
    {
        return ['templateFiles' => $this->getTemplateService()->getTemplateFiles()];
    }

    /**
     * @Route("/preview/{id}", name="wizin_simple_cms_admin_preview")
     */
    public function previewAction($id)
    {
        $template = $this->getTemplateService();
        // retrieve Content instance by $id
        $content = $this->getContentRepository()->find($id);
        if (is_null($content)) {
            // invalid url
            throw new NotFoundHttpException();
        }
        // create response
        $response = new Response();
        $responseContent = $template->generateResponseContent($content);
        $response->setContent($responseContent);

        return $response;
    }

    /**
     * @param Content $content
     * @param null $templateFile
     * @return \Symfony\Component\Form\Form
     */
    protected function createContentForm(Content $content, $templateFile = null)
    {
        $parameters = [];
        foreach ($this->getTemplateService()->getPlaceholders($templateFile) as $placeholder) {
            $parameters[$placeholder] = null;
        }
        $content->setParameters(array_merge($parameters, (array) $content->getParameters()));
        if (is_null($templateFile) === false) {
            $content->setTemplateFile($templateFile);
        }

        return  $this->createForm(new ContentType(), $content);
    }

    /**
     * @return mixed|string
     */
    protected function getBaseUrl()
    {
        $baseUrl = $this->container->getParameter('wizin_simple_cms.base_url');
        if (is_null($baseUrl)) {
            $baseUrl = preg_replace(
                '@' .preg_quote($this->getRequest()->getBaseUrl()) .'@',
                '',
                $this->getRequest()->getUriForPath('/')
            );
        }
        if (substr($baseUrl, -1) === '/') {
            $baseUrl = substr($baseUrl, 0, -1);
        }

        return $baseUrl;
    }
}
