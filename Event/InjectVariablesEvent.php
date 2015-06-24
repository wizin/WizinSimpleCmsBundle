<?php
namespace Wizin\Bundle\SimpleCmsBundle\Event;

use Symfony\Component\EventDispatcher\Event as BaseEvent;
use Wizin\Bundle\SimpleCmsBundle\Entity\ContentInterface;

class InjectVariablesEvent extends BaseEvent
{
    /**
     * @var \Wizin\Bundle\SimpleCmsBundle\Entity\ContentInterface
     */
    protected $content;

    /**
     * @var array
     */
    protected $variables = [];

    /**
     * @param ContentInterface $content
     */
    public function __construct(ContentInterface $content)
    {
        $this->content = $content;
    }

    /**
     * @return ContentInterface
     */
    public function getContent()
    {
        $content = clone $this->content;

        return $content;
    }

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

