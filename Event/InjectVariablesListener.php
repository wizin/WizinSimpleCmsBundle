<?php
namespace Wizin\Bundle\SimpleCmsBundle\Event;

use Symfony\Component\EventDispatcher\EventDispatcher;

class InjectVariablesListener
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
     * @param InjectVariablesEvent $event
     */
    public function onInjectVariables(InjectVariablesEvent $event)
    {
        // do something...
    }
}

