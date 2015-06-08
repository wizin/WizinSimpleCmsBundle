<?php
/**
 * ContentManager
 */
namespace Wizin\Bundle\SimpleCmsBundle\Service;

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
     * @param Content $content
     * @return bool
     */
    public function save(Content $content)
    {
        // duplicate check
        if ($this->getClassLoader()->getContentRepository()->isDuplicated($content)) {
            throw new DuplicateContentException();
        }
        // persist entity
        $this->getEntityManager()->persist($content);
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
}

