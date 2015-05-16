<?php

namespace Wizin\Bundle\SimpleCmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Content
 *
 * @ORM\Table(
 *   name="content",
 *   indexes={
 *     @ORM\Index(columns={"path_info", "active"}),
 *   }
 * )
 * @ORM\Entity(repositoryClass="Wizin\Bundle\SimpleCmsBundle\Repository\ContentRepository")
 */
class Content
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", length=36)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

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
     * @ORM\Column(name="parameters", type="text")
     * @Assert\NotNull()
     * @Assert\Type(type="string", message="The value {{ value }} is not a valid {{ type }}.")
     */
    protected $parameters;

    /**
     * @var string
     *
     * @ORM\Column(name="template", type="string", length=255)
     * @Assert\NotNull()
     * @Assert\Type(type="string", message="The value {{ value }} is not a valid {{ type }}.")
     */
    protected $template;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", options={"default":false})
     * @Assert\NotNull()
     * @Assert\Type(type="bool", message="The value {{ value }} is not a valid {{ type }}.")
     */
    protected $active;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    protected $updatedAt;


    /**
     * Set id
     *
     * @param string $id
     * @return Content
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

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
     * @param string $parameters
     * @return Content
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Get parameters
     *
     * @return string 
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Set template
     *
     * @param string $template
     * @return Content
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get template
     *
     * @return string 
     */
    public function getTemplate()
    {
        return $this->template;
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

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Content
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Content
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
