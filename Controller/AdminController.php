<?php

namespace Wizin\Bundle\SimpleCmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Wizin\Bundle\SimpleCmsBundle\Entity\Content;
use Wizin\Bundle\SimpleCmsBundle\Form\ContentType;

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
        return [];
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
        $parameters = [];
        foreach ($this->getTemplateService()->getPlaceholders($templateFile) as $placeholder) {
            $parameters[$placeholder] = null;
        }
        $content->setParameters($parameters);
        $content->setTemplateFile($templateFile);
        $form = $this->createForm(new ContentType(), $content);
        if ($this->getRequest()->isMethod('POST')) {
            $form->handleRequest($this->getRequest());
            if ($form->isValid()) {
                // TODO: persist entity
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
     * @return \Wizin\Bundle\SimpleCmsBundle\Service\Template
     */
    protected function getTemplateService()
    {
        return $this->get('wizin_simple_cms.template');
    }
}
