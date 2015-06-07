<?php
/**
 * Base controller class for WizinSimpleCmsBundle
 */
namespace Wizin\Bundle\SimpleCmsBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Wizin\Bundle\BaseBundle\Controller\Controller as BaseController;
use Wizin\Bundle\SimpleCmsBundle\Entity\Content;

/**
 * Class Controller
 * @package Wizin\Bundle\SimpleCmsBundle\Controller
 * @author Makoto Hashiguchi <gusagi@gmail.com>
 */
class Controller extends BaseController
{
    /**
     * @param Content $content
     * @return Response
     */
    protected function sendContent(Content $content)
    {
        $response = new Response();
        $responseContent = $this->getTemplateHandler()->generateResponseContent($content);
        $response->setContent($responseContent);

        return $response;
    }

    /**
     * @return \Wizin\Bundle\SimpleCmsBundle\Service\TemplateHandler
     */
    protected function getTemplateHandler()
    {
        return $this->get('wizin_simple_cms.template_handler');
    }

    /**
     * @return \Wizin\Bundle\SimpleCmsBundle\Service\ClassLoader
     */
    protected function getClassLoader()
    {
        return $this->get('wizin_simple_cms.class_loader');
    }
}

