<?php

namespace Wizin\Bundle\SimpleCmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ContentUpdateHistory
 *
 * @ORM\Table(
 *   name="content_update_history",
 *   indexes={
 *     @ORM\Index(columns={"content_id"}),
 *   },
 *   uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"content_id", "tag"})
 *   }
 * )
 * @ORM\Entity(repositoryClass="Wizin\Bundle\SimpleCmsBundle\Repository\ContentUpdateHistoryRepository")
 */
class ContentUpdateHistory extends AbstractContent implements ContentUpdateHistoryInterface
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
     * @var string
     *
     * @ORM\Column(name="tag", type="string", length=14)
     * @Assert\NotNull()
     * @Assert\Type(type="string", message="The value {{ value }} is not a valid {{ type }}.")
     */
    protected $tag;


    /**
     * Set id
     *
     * @param string $id
     * @return ContentUpdateHistory
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
     * @return ContentUpdateHistory
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

    /**
     * Set tag
     *
     * @param string $tag
     * @return ContentUpdateHistory
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }
}
