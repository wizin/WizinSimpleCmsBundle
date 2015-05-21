<?php
namespace Wizin\Bundle\SimpleCmsBundle\Event;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

class FilterControllerListener
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    protected $dispatcher;

    /**
     * @param EventDispatcher $dispatcher
     */
    public function setEventDispatcher(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        if (is_array($controller) === false) {
            return;
        }
        switch (get_class($controller[0])) {
            case 'Wizin\Bundle\SimpleCmsBundle\Controller\FrontController':
                $event = new FrontControllerEvent(
                    $event->getKernel(),
                    $controller,
                    $event->getRequest(),
                    $event->getRequestType()
                );
                $this->dispatcher->dispatch(Event::ON_FRONT_CONTROLLER, $event);
                break;
            case 'Wizin\Bundle\SimpleCmsBundle\Controller\AdminController':
                $event = new AdminControllerEvent(
                    $event->getKernel(),
                    $controller,
                    $event->getRequest(),
                    $event->getRequestType()
                );
                $this->dispatcher->dispatch(Event::ON_ADMIN_CONTROLLER, $event);
                break;
        }
    }
}