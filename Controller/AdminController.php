<?php

namespace Wizin\Bundle\SimpleCmsBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Wizin\Bundle\SimpleCmsBundle\Entity\ContentInterface;
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
    public function indexAction(Request $request)
    {
        return $this->forward('WizinSimpleCmsBundle:Admin:list');
    }

    /**
     * @Route("/list", name="wizin_simple_cms_admin_list")
     * @Template()
     */
    public function listAction(Request $request)
    {
        $contentsList = $this->getContentManager()->retrieveContentsList();
        $baseUrl = $this->getBaseUrl($request);

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
    public function addAction(Request $request, $templateFile)
    {
        if ($templateFile === '') {
            return $this->forward('WizinSimpleCmsBundle:Admin:selectTemplateFile');
        }
        $entityClass = $this->getClassLoader()->getContentRepository()->getClassName();
        /** @var \Wizin\Bundle\SimpleCmsBundle\Entity\ContentInterface $content */
        $content = new $entityClass();
        $form = $this->createContentForm($content, $templateFile);
        if ($request->isMethod('POST')) {
            if ($this->save($request, $content, $form)) {
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
    public function editAction(Request $request, $id)
    {
        /** @var \Wizin\Bundle\SimpleCmsBundle\Entity\ContentInterface $content */
        $content = $this->getClassLoader()->getContentRepository()->find($id);

        return $this->edit($request, $content);
    }

    /**
     * @Route("/selectTemplateFile", name="wizin_simple_cms_admin_select_template_file")
     * @Template()
     */
    public function selectTemplateFileAction(Request $request)
    {
        return ['templateFiles' => $this->getTemplateHandler()->getTemplateFiles()];
    }

    /**
     * @Route("/preview/{id}", name="wizin_simple_cms_admin_preview")
     */
    public function previewAction(Request $request, $id)
    {
        // retrieve content instance by $id
        /** @var \Wizin\Bundle\SimpleCmsBundle\Entity\ContentInterface $content */
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
    public function draftEditAction(Request $request, $id)
    {
        /** @var \Wizin\Bundle\SimpleCmsBundle\Entity\DraftContentInterface $draft */
        $draft = $this->getClassLoader()->getDraftContentRepository()->find($id);
        $content = $this->getContentConverter()->convertFromDraft($draft);

        return $this->edit($request, $content);
    }

    /**
     * @Route("/draftPreview/{id}", name="wizin_simple_cms_admin_draft_preview")
     */
    public function draftPreviewAction(Request $request, $id)
    {
        /** @var \Wizin\Bundle\SimpleCmsBundle\Entity\DraftContentInterface $draft */
        $draft = $this->getClassLoader()->getDraftContentRepository()->find($id);
        $content = $this->getContentConverter()->convertFromDraft($draft);
        if (is_null($content)) {
            // invalid url
            throw new NotFoundHttpException();
        }

        return $this->sendContent($content);
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
        $contentFormType = $this->getClassLoader()->getContentFormTypeFQCN();

        return  $this->createForm($contentFormType, $content);
    }

    /**
     * @param Request $request
     * @param null|ContentInterface $content
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function edit(Request $request, ContentInterface $content)
    {
        if (is_null($content)) {
            // invalid url
            throw new NotFoundHttpException();
        }
        $form = $this->createContentForm($content, $content->getTemplateFile());
        if ($request->isMethod('POST')) {
            if ($this->save($request, $content, $form)) {
                return $this->redirect($this->generateUrl('wizin_simple_cms_admin_index'));
            }
        }
        $options = $this->getTemplateHandler()->getOptions($content->getTemplateFile());

        return ['form' => $form->createView(), 'options' => $options];
    }

    /**
     * @param Request $request
     * @param ContentInterface $content
     * @param Form $form
     * @return bool
     */
    protected function save(Request $request, ContentInterface $content, Form $form)
    {
        $result = false;
        $form->handleRequest($request);
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
     * @param Request $request
     * @return mixed|string
     */
    protected function getBaseUrl(Request $request)
    {
        $baseUrl = $this->container->getParameter('wizin_simple_cms.base_url');
        if (is_null($baseUrl)) {
            $baseUrl = preg_replace(
                '@' .preg_quote($request->getBaseUrl()) .'@',
                '',
                $request->getUriForPath('/')
            );
        }
        if (substr($baseUrl, -1) === '/') {
            $baseUrl = substr($baseUrl, 0, -1);
        }

        return $baseUrl;
    }
}
