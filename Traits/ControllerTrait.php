<?php
/**
 * Trait for Controller of WizinSimpleCmsBundle
 */
namespace Wizin\Bundle\SimpleCmsBundle\Traits;

use Wizin\Bundle\BaseBundle\Traits\ControllerTrait as BaseTrait;

/**
 * Trait for Controller
 *
 * @author Makoto Hashiguchi <gusagi@gmail.com>
 */
trait ControllerTrait
{
    /**
     * \Wizin\Bundle\BaseBundle\Traits\ControllerTrait
     */
    use BaseTrait;

    /**
     * @return \Wizin\Bundle\SimpleCmsBundle\Repository\ContentRepository
     */
    protected function getContentRepository()
    {
        return $this->getEntityManager()->getRepository('WizinSimpleCmsBundle:Content');
    }

    /**
     * @return \Wizin\Bundle\SimpleCmsBundle\Service\Template
     */
    protected function getTemplateService()
    {
        return $this->get('wizin_simple_cms.template');
    }
}

 