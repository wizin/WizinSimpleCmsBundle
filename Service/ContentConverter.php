<?php
/**
 * ContentConverter
 */
namespace Wizin\Bundle\SimpleCmsBundle\Service;

use Wizin\Bundle\BaseBundle\Service\Service;
use Wizin\Bundle\SimpleCmsBundle\Entity\Content;

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
     * @return \Wizin\Bundle\SimpleCmsBundle\Service\ClassLoader
     */
    protected function getClassLoader()
    {
        return $this->container->get('wizin_simple_cms.class_loader');
    }
}

