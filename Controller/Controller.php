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
     * @return \Wizin\Bundle\SimpleCmsBundle\Service\TemplateHandler
     */
    protected function getTemplateHandler()
    {
        return $this->get('wizin_simple_cms.template_handler');
    }

    /**
     * @return \Wizin\Bundle\SimpleCmsBundle\Service\ClassLoader
     */
    protected function getClassLoader()
    {
        return $this->get('wizin_simple_cms.class_loader');
    }
}

