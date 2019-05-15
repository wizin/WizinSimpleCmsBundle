<?php
/**
 * ContentManager
 */
namespace Wizin\Bundle\SimpleCmsBundle\Service;

use Doctrine\ORM\Query;
use Wizin\Bundle\BaseBundle\Service\Service;
use Wizin\Bundle\SimpleCmsBundle\Entity\Content;
use Wizin\Bundle\SimpleCmsBundle\Exception\DuplicateContentException;

/**
 * Class ContentManager
 * @package Wizin\Bundle\SimpleCmsBundle\Service
 */
class ContentManager extends Service
{
    /**
     * @param string $pathInfo
     * @return \Wizin\Bundle\SimpleCmsBundle\Entity\ContentInterface
     */
    public function retrieveEnableContent($pathInfo)
    {
        /** @var \Wizin\Bundle\SimpleCmsBundle\Entity\ContentInterface $content */
        $content = $this->getClassLoader()->getContentRepository()->retrieveEnableContent($pathInfo);

        return $content;
    }

    /**
     * @return array
     */
    public function retrieveContentsList()
    {
        $contentsList = [];
        $contents = $this->getClassLoader()->getContentRepository()->findAll();
        /** @var \Wizin\Bundle\SimpleCmsBundle\Entity\Content[] $contents */
        foreach ($contents as $content) {
            /** @var \Wizin\Bundle\SimpleCmsBundle\Entity\DraftContent $draftContent */
            $draftContent = $this->getClassLoader()->getDraftContentRepository()
                ->findOneBy(['contentId' => $content->getId()]);
            $contentsList[] = [
                'content' => $content,
                'draft' => $draftContent,
            ];
        }

        return $contentsList;
    }

    /**
     * @param Content $content
     * @param bool isDraft
     * @return bool
     */
    public function save(Content $content, $isDraft = false)
    {
        if ($isDraft) {
            // convert Content -> DraftContent
            $draft = $this->getContentConverter()->convertToDraft($content);
            // persist DraftContent entity
            $this->getEntityManager()->persist($draft);
            // refresh Content entity
            $this->getEntityManager()->refresh($content);
        } else {
            // duplicate check
            if ($this->getClassLoader()->getContentRepository()->isDuplicated($content)) {
                throw new DuplicateContentException();
            }
            // persist Content entity
            $this->getEntityManager()->persist($content);
            // remove draft
            $draft = $this->getClassLoader()->getDraftContentRepository()->findOneBy(['contentId' => $content->getId()]);
            if (is_null($draft) === false) {
                $this->getEntityManager()->remove($draft);
            }
        }
        $this->getEntityManager()->flush();

        return true;
    }

    /**
     * @return \Wizin\Bundle\SimpleCmsBundle\Service\ClassLoader
     */
    protected function getClassLoader()
    {
        return $this->container->get('wizin_simple_cms.class_loader');
    }

    /**
     * @return \Wizin\Bundle\SimpleCmsBundle\Service\ContentConverter
     */
    protected function getContentConverter()
    {
        return $this->container->get('wizin_simple_cms.content_converter');
    }
}

