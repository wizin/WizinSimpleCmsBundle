<?php
namespace Wizin\Bundle\SimpleCmsBundle\Event;

use Symfony\Component\EventDispatcher\Event as BaseEvent;

class InjectVariablesEvent extends BaseEvent
{
    /**
     * @var array
     */
    protected $variables = [];

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function setVariable($key, $value)
    {
        $this->variables[$key] = $value;

        return $this;
    }

    /**
     * @param $key
     * @return null|mixed
     */
    public function getVariable($key)
    {
        return isset($this->variables[$key]) ? $this->variables[$key] : null;
    }

    /**
     * @param array $variables
     * @return $this
     */
    public function setVariables(array $variables)
    {
        $this->variables = $variables;

        return $this;
    }

    /**
     * @return array
     */
    public function getVariables()
    {
        return $this->variables;
    }
}

