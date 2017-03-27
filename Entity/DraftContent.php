<?php

namespace Wizin\Bundle\SimpleCmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DraftContent
 *
 * @ORM\Table(
 *   name="draft_content",
 *   indexes={
 *     @ORM\Index(columns={"path_info", "active"}),
 *   },
 *   uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"content_id"})
 *   }
 * )
 * @ORM\Entity(repositoryClass="Wizin\Bundle\SimpleCmsBundle\Repository\DraftContentRepository")
 */
class DraftContent extends AbstractContent implements DraftContentInterface
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
     * @ORM\Column(name="content_id", type="string", length=36)
     * @Assert\NotNull()
     * @Assert\Type(type="string", message="The value {{ value }} is not a valid {{ type }}.")
     */
    protected $contentId;


    /**
     * Set id
     *
     * @param string $id
     * @return DraftContent
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
     * Set content id
     *
     * @param string $contentId
     * @return DraftContent
     */
    public function setContentId($contentId)
    {
        $this->contentId = $contentId;

        return $this;
    }

    /**
     * Get content id
     *
     * @return string
     */
    public function getContentId()
    {
        return $this->contentId;
    }
}

