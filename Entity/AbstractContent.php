<?php

namespace Wizin\Bundle\SimpleCmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Wizin\Bundle\BaseBundle\Traits\Entity\Timestampable;

/**
 * AbstractContent
 */
abstract class AbstractContent
{
    /**
     * \Wizin\Bundle\BaseBundle\Traits\Entity\Timestampable
     */
    use Timestampable;

    /**
     * @var string
     *
     * @ORM\Column(name="path_info", type="string", length=255)
     * @Assert\NotNull()
     * @Assert\Type(type="string", message="The value {{ value }} is not a valid {{ type }}.")
     */
    protected $pathInfo;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\NotNull()
     * @Assert\Type(type="string", message="The value {{ value }} is not a valid {{ type }}.")
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="parameters", type="json_array")
     * @Assert\NotNull()
     * @Assert\Type(type="array", message="The value {{ value }} is not a valid {{ type }}.")
     */
    protected $parameters;

    /**
     * @var string
     *
     * @ORM\Column(name="template_file", type="string", length=255)
     * @Assert\NotNull()
     * @Assert\Type(type="string", message="The value {{ value }} is not a valid {{ type }}.")
     */
    protected $templateFile;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", options={"default":false})
     * @Assert\NotNull()
     * @Assert\Type(type="bool", message="The value {{ value }} is not a valid {{ type }}.")
     */
    protected $active;


    /**
     * Set pathInfo
     *
     * @param string $pathInfo
     * @return Content
     */
    public function setPathInfo($pathInfo)
    {
        $this->pathInfo = $pathInfo;

        return $this;
    }

    /**
     * Get pathInfo
     *
     * @return string
     */
    public function getPathInfo()
    {
        return $this->pathInfo;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Content
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set parameters
     *
     * @param array $parameters
     * @return Content
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = array_map(
            function ($string) {
                return strtr($string, array_fill_keys(["\r\n", "\r", "\n"], "\n"));
            },
            $parameters
        );

        return $this;
    }

    /**
     * Get parameters
     *
     * @return mixed
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Set templateFile
     *
     * @param string $templateFile
     * @return Content
     */
    public function setTemplateFile($templateFile)
    {
        $this->templateFile = $templateFile;

        return $this;
    }

    /**
     * Get templateFile
     *
     * @return string
     */
    public function getTemplateFile()
    {
        return $this->templateFile;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return Content
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }
}

