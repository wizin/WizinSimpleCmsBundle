<?php
namespace Wizin\Bundle\SimpleCmsBundle\Event;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class InjectVariablesListener
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function setEventDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param InjectVariablesEvent $event
     */
    public function onInjectVariables(InjectVariablesEvent $event)
    {
        // do something...
    }
}

