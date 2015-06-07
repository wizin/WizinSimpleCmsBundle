<?php

namespace Wizin\Bundle\SimpleCmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Content
 *
 * @ORM\Table(
 *   name="content",
 *   indexes={
 *     @ORM\Index(columns={"path_info", "active"}),
 *   },
 *   uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"path_info"})
 *   }
 * )
 * @ORM\Entity(repositoryClass="Wizin\Bundle\SimpleCmsBundle\Repository\ContentRepository")
 */
class Content extends AbstractContent
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
}
