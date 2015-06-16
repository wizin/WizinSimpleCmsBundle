<?php

namespace Wizin\Bundle\SimpleCmsBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Wizin\Bundle\SimpleCmsBundle\Entity\ContentInterface;
use Wizin\Bundle\SimpleCmsBundle\Entity\Content;
use Wizin\Bundle\SimpleCmsBundle\Exception\DuplicateContentException;

/**
 * Class AdminController
 * @package Wizin\Bundle\SimpleCmsBundle\Controller
 */
class AdminController extends Controller
{
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
        $contentsList = $this->getContentManager()->retrieveContentsList();
        $baseUrl = $this->getBaseUrl();

        return ['contentsList' => $contentsList, 'baseUrl' => $baseUrl];
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
        $entityClass = $this->getClassLoader()->getContentRepository()->getClassName();
        /** @var \Wizin\Bundle\SimpleCmsBundle\Entity\Content $content */
        $content = new $entityClass();
        $form = $this->createContentForm($content, $templateFile);
        if ($this->getRequest()->isMethod('POST')) {
            if ($this->save($content, $form)) {
                return $this->redirect($this->generateUrl('wizin_simple_cms_admin_index'));
            }
        }
        $options = $this->getTemplateHandler()->getOptions($templateFile);

        return ['form' => $form->createView(), 'options' => $options];
    }

    /**
     * @Route(
     *   "/edit/{id}",
     *   name="wizin_simple_cms_admin_edit",
     * )
     * @Template()
     */
    public function editAction($id)
    {
        /** @var \Wizin\Bundle\SimpleCmsBundle\Entity\Content $content */
        $content = $this->getClassLoader()->getContentRepository()->find($id);

        return $this->edit($content);
    }

    /**
     * @Route("/selectTemplateFile", name="wizin_simple_cms_admin_select_template_file")
     * @Template("WizinSimpleCmsBundle:Admin:select_template_file.html.twig")
     */
    public function selectTemplateFileAction()
    {
        return ['templateFiles' => $this->getTemplateHandler()->getTemplateFiles()];
    }

    /**
     * @Route("/preview/{id}", name="wizin_simple_cms_admin_preview")
     */
    public function previewAction($id)
    {
        // retrieve content instance by $id
        /** @var \Wizin\Bundle\SimpleCmsBundle\Entity\Content $content */
        $content = $this->getClassLoader()->getContentRepository()->find($id);
        if (is_null($content)) {
            // invalid url
            throw new NotFoundHttpException();
        }

        return $this->sendContent($content);
    }

    /**
     * @Route(
     *   "/draftEdit/{id}",
     *   name="wizin_simple_cms_admin_draft_edit",
     * )
     * @Template()
     */
    public function draftEditAction($id)
    {
        /** @var \Wizin\Bundle\SimpleCmsBundle\Entity\DraftContent $draft */
        $draft = $this->getClassLoader()->getDraftContentRepository()->find($id);
        $content = $this->getContentConverter()->convertFromDraft($draft);

        return $this->edit($content);
    }

    /**
     * @param ContentInterface $content
     * @param null $templateFile
     * @return \Symfony\Component\Form\Form
     */
    protected function createContentForm(ContentInterface $content, $templateFile = null)
    {
        $hash = [];
        $parameters = (array) $content->getParameters();
        foreach ($this->getTemplateHandler()->getPlaceholders($templateFile) as $placeholder) {
            if (isset($parameters[$placeholder])) {
                $hash[$placeholder] = $parameters[$placeholder];
            } else {
                $hash[$placeholder] = null;
            }
        }
        $content->setParameters($hash);
        if (is_null($templateFile) === false) {
            $content->setTemplateFile($templateFile);
        }
        $contentFormType = $this->getClassLoader()->getContentFormType();

        return  $this->createForm(new $contentFormType(), $content);
    }

    /**
     * @param Content $content
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function edit(Content $content)
    {
        /** @var \Wizin\Bundle\SimpleCmsBundle\Entity\Content $content */
        if (is_null($content)) {
            // invalid url
            throw new NotFoundHttpException();
        }
        $form = $this->createContentForm($content, $content->getTemplateFile());
        if ($this->getRequest()->isMethod('POST')) {
            if ($this->save($content, $form)) {
                return $this->redirect($this->generateUrl('wizin_simple_cms_admin_index'));
            }
        }
        $options = $this->getTemplateHandler()->getOptions($content->getTemplateFile());

        return ['form' => $form->createView(), 'options' => $options];
    }

    /**
     * @param ContentInterface $content
     * @param Form $form
     * @return bool
     */
    protected function save(ContentInterface $content, Form $form)
    {
        $result = false;
        $form->handleRequest($this->getRequest());
        if ($form->isValid()) {
            try {
                $isDraft = $form->get('draft')->isClicked();
                if ($this->getContentManager()->save($content, $isDraft) === true) {
                    $this->getTemplateHandler()->removeCache($content);
                    $result = true;
                }
            } catch (DuplicateContentException $exception) {
                $form->addError(new FormError(
                    $this->container->getParameter('wizin_simple_cms.message.error.duplicate')));
            }
        }

        return $result;
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
