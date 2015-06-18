<?php
/**
 * Base controller class for WizinSimpleCmsBundle
 */
namespace Wizin\Bundle\SimpleCmsBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Wizin\Bundle\BaseBundle\Controller\Controller as BaseController;
use Wizin\Bundle\SimpleCmsBundle\Entity\ContentInterface;
use Wizin\Bundle\SimpleCmsBundle\Event\Event;
use Wizin\Bundle\SimpleCmsBundle\Event\InjectVariablesEvent;

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
        // dispatch InjectVariablesEvent
        $event = new InjectVariablesEvent();
        $key = Event::ON_INJECT_VARIABLES;
        foreach ($content->getUniqueColumns() as $column) {
            $getter = 'get' .ucfirst($column);
            $key .= '.' .$content->$getter();
        }
        /** @var \Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher $dispatcher */
        $dispatcher = $this->container->get('event_dispatcher');
        $dispatcher->dispatch($key, $event);
        // render
        $cache = $this->getTemplateHandler()->getTemplateCache($content);
        $this->container->get('twig.loader')->addPath(dirname($cache));

        return $this->render(
            basename($cache),
            [
                'title' => $content->getTitle(),
                'vars' => $event->getVariables(),
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

    /**
     * @return \Wizin\Bundle\SimpleCmsBundle\Service\ContentConverter
     */
    protected function getContentConverter()
    {
        return $this->get('wizin_simple_cms.content_converter');
    }
}

