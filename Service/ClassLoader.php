<?php
/**
 * ClassLoader
 */
namespace Wizin\Bundle\SimpleCmsBundle\Service;

use Wizin\Bundle\BaseBundle\Service\Service;
use Wizin\Bundle\SimpleCmsBundle\Form\ContentType;

/**
 * Class ClassLoader
 * @package Wizin\Bundle\SimpleCmsBundle\Service
 */
class ClassLoader extends Service
{
    /**
     * @return \Wizin\Bundle\SimpleCmsBundle\Repository\ContentRepository
     */
    public function getContentRepository()
    {
        return $this->getEntityManager()->getRepository('WizinSimpleCmsBundle:Content');
    }

    /**
     * @return \Wizin\Bundle\SimpleCmsBundle\Repository\DraftContentRepository
     */
    public function getDraftContentRepository()
    {
        return $this->getEntityManager()->getRepository('WizinSimpleCmsBundle:DraftContent');
    }

    /**
     * @return \Wizin\Bundle\SimpleCmsBundle\Form\ContentType
     */
    public function getContentFormType()
    {
        return new ContentType();
    }
}
