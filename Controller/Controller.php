<?php
/**
 * Base controller class for WizinSimpleCmsBundle
 */
namespace Wizin\Bundle\SimpleCmsBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Wizin\Bundle\BaseBundle\Controller\Controller as BaseController;
use Wizin\Bundle\SimpleCmsBundle\Entity\ContentInterface;

/**
 * Class Controller
 * @package Wizin\Bundle\SimpleCmsBundle\Controller
 * @author Makoto Hashiguchi <gusagi@gmail.com>
 */
class Controller extends BaseController
{
    /**
     * @param ContentInterface $content
     * @return Response
     */
    protected function sendContent(ContentInterface $content)
    {
        $cache = $this->getTemplateHandler()->getTemplateCache($content);
        $this->container->get('twig.loader')->addPath(dirname($cache));

        return $this->render(
            basename($cache),
            [
                'title' => $content->getTitle(),
            ]
        );
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

    /**
     * @return \Wizin\Bundle\SimpleCmsBundle\Service\ContentManager
     */
    protected function getContentManager()
    {
        return $this->get('wizin_simple_cms.content_manager');
    }
}

