<?php
/**
 * TemplateSevice
 */
namespace Wizin\Bundle\SimpleCmsBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Template
 * @package Wizin\Bundle\SimpleCmsBundle\Service
 */
class Template
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
}