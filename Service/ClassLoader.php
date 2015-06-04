<?php
/**
 * ClassLoader
 */
namespace Wizin\Bundle\SimpleCmsBundle\Service;

use Wizin\Bundle\BaseBundle\Service\Service;

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
}
