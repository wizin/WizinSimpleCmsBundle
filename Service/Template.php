<?php
/**
 * TemplateSevice
 */
namespace Wizin\Bundle\SimpleCmsBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Finder;

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
     * @var string path of template directory
     */
    protected $templateDir;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->templateDir = dirname(__DIR__) . '/Resources/templates';
    }

    /**
     * @param null|string $templateDir
     * @return $this
     */
    public function setTemplateDir($templateDir)
    {
        if (is_null($templateDir) === false) {
            $this->templateDir = $templateDir;
        }

        return $this;
    }

    /**
     * @return string path of template directory
     */
    public function getTemplateDir()
    {
        return $this->templateDir;
    }

    /**
     * @return string[] template file path without template directory path
     */
    public function getTemplateFiles()
    {
        $extension = $this->container->getParameter('wizin_simple_cms.template_extension');
        $finder = new Finder();
        $finder
            ->in($this->getTemplateDir())
            ->files()
            ->followLinks()
            ->sortByName()
            ->name('*.' . $extension)
        ;
        $templateFiles = [];
        foreach ($finder as $file) {
            /** @var \SplFileInfo $file */
            $templateFile = str_replace($this->templateDir, '', $file->getPathname());
            if (substr($templateFile, 0, 1) === '/') {
                $templateFile = substr($templateFile, 1);
            }
            $templateFiles[] = $templateFile;
        }

        return $templateFiles;
    }
}