<?php
/**
 * Base controller class for WizinSimpleCmsBundle
 */
namespace Wizin\Bundle\SimpleCmsBundle\Controller;

use Wizin\Bundle\BaseBundle\Controller\Controller as BaseController;

/**
 * Class Controller
 * @package Wizin\Bundle\SimpleCmsBundle\Controller
 * @author Makoto Hashiguchi <gusagi@gmail.com>
 */
class Controller extends BaseController
{
    /**
     * @return \Wizin\Bundle\SimpleCmsBundle\Service\Template
     */
    protected function getTemplateService()
    {
        return $this->get('wizin_simple_cms.template');
    }

    /**
     * @return \Wizin\Bundle\SimpleCmsBundle\Service\Content
     */
    protected function getContentService()
    {
        return $this->get('wizin_simple_cms.content');
    }
}

