<?php

namespace Wizin\Bundle\SimpleCmsBundle\Entity;

interface ContentUpdateHistoryInterface
{
    /**
     * Set id
     *
     * @param string $id
     */
    public function setId($id);

    /**
     * Get id
     */
    public function getId();

    /**
     * Set contentId
     *
     * @param string $contentId
     */
    public function setContentId($contentId);

    /**
     * Get contentId
     */
    public function getContentId();

    /**
     * Set tag
     *
     * @param string $tag
     */
    public function setTag($tag);

    /**
     * Get tag
     */
    public function getTag();

    /**
     * Set pathInfo
     *
     * @param string $pathInfo
     */
    public function setPathInfo($pathInfo);

    /**
     * Get pathInfo
     */
    public function getPathInfo();

    /**
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title);

    /**
     * Get title
     */
    public function getTitle();

    /**
     * Set parameters
     *
     * @param array $parameters
     */
    public function setParameters(array $parameters);

    /**
     * Get parameters
     */
    public function getParameters();

    /**
     * Set templateFile
     *
     * @param string $templateFile
     */
    public function setTemplateFile($templateFile);

    /**
     * Get templateFile
     */
    public function getTemplateFile();

    /**
     * Set active
     *
     * @param boolean $active
     */
    public function setActive($active);

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive();

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt);

    /**
     * Get createdAt
     */
    public function getCreatedAt();

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt);

    /**
     * Get updatedAt
     */
    public function getUpdatedAt();
}
