<?php
/**
 * ContentConverter
 */
namespace Wizin\Bundle\SimpleCmsBundle\Service;

use Wizin\Bundle\BaseBundle\Service\Service;
use Wizin\Bundle\SimpleCmsBundle\Entity\Content;
use Wizin\Bundle\SimpleCmsBundle\Entity\DraftContent;
use Wizin\Bundle\SimpleCmsBundle\Exception\OrphanDraftException;

/**
 * Class ContentConverter
 * @package Wizin\Bundle\SimpleCmsBundle\Service
 */
class ContentConverter extends Service
{
    /**
     * @param Content $content
     * @return DraftContent $draft
     */
    public function convertToDraft(Content $content)
    {
        $draftContentRepository = $this->getClassLoader()->getDraftContentRepository();
        /** @var \Wizin\Bundle\SimpleCmsBundle\Entity\DraftContent $draft */
        $draft = $draftContentRepository->findOneBy(['contentId' => $content->getId()]);
        if (is_null($draft)) {
            $entityClass = $this->getClassLoader()->getDraftContentRepository()->getClassName();
            $draft = new $entityClass();
        }
        $draft
            ->setContentId($content->getId())
            ->setPathInfo($content->getPathInfo())
            ->setTitle($content->getTitle())
            ->setParameters($content->getParameters())
            ->setTemplateFile($content->getTemplateFile())
            ->setActive($content->getActive())
        ;

        return $draft;
    }

    /**
     * @param DraftContent $draft
     * @return Content
     */
    public function convertFromDraft(DraftContent $draft)
    {
        $contentRepository = $this->getClassLoader()->getContentRepository();
        /** @var \Wizin\Bundle\SimpleCmsBundle\Entity\Content $content */
        $content = $contentRepository->find($draft->getContentId());
        if (is_null($content)) {
            throw new OrphanDraftException();
        }
        $content
            ->setPathInfo($draft->getPathInfo())
            ->setTitle($draft->getTitle())
            ->setParameters($draft->getParameters())
            ->setTemplateFile($draft->getTemplateFile())
            ->setActive($draft->getActive())
        ;

        return $content;
    }

    /**
     * @return \Wizin\Bundle\SimpleCmsBundle\Service\ClassLoader
     */
    protected function getClassLoader()
    {
        return $this->container->get('wizin_simple_cms.class_loader');
    }
}

