<?php
/**
 * TemplateSevice
 */
namespace Wizin\Bundle\SimpleCmsBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Wizin\Bundle\SimpleCmsBundle\Entity\Content;

/**
 * Class Template
 * @package Wizin\Bundle\SimpleCmsBundle\Service
 */
class Template
{
    /**
     * regex index for placeholder
     */
    const PLACEHOLDER_INDEX = 3;

    /**
     * cache directory name
     */
    const CACHE_DIR_NAME = 'cms';

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

    /**
     * @param $templateFile
     * @return string path to template file
     */
    public function getTemplateFilePath($templateFile)
    {
        return realpath($this->templateDir .'/' .$templateFile);
    }

    /**
     * @param $templateFile
     * @return bool
     */
    public function isExists($templateFile)
    {
        $filesystem = new Filesystem();

        return $filesystem->exists($this->getTemplateFilePath($templateFile));
    }

    /**
     * @param $templateFile
     * @return string[] placeholders
     */
    public function getPlaceholders($templateFile)
    {
        $pattern = $this->getPlaceholderRegex();
        preg_match_all($pattern, $this->getTemplateSource($templateFile), $matches);
        if (isset($matches[static::PLACEHOLDER_INDEX]) && is_array($matches[static::PLACEHOLDER_INDEX])) {
            $placeholders = $matches[static::PLACEHOLDER_INDEX];
        } else {
            $placeholders = [];
        }

        return array_unique($placeholders);
    }

    /**
     * @param Content $content
     * @return string $responseContent content string for response
     */
    public function generateResponseContent(Content $content)
    {
        $filesystem = new Filesystem();
        $cachePath = $this->getCachePath($content);
        if ($filesystem->exists($cachePath) === false) {
            $source = $this->getTemplateSource($content->getTemplateFile());
            $source = $this->replaceSource($source, $content->getParameters());
            $filesystem->dumpFile($cachePath, $source);
        }
        $twig = $this->container->get('twig');
        $responseContent = $twig->render(
            $cachePath,
            [
                'title' => $content->getTitle(),
            ]
        );

        return $responseContent;
    }

    /**
     * @param Content $content
     */
    public function removeCache(Content $content)
    {
        $filesystem = new Filesystem();
        $cache = $this->getCachePath($content);
        $filesystem->remove($cache);
    }

    /**
     * @return string regex pattern for placeholder
     */
    protected function getPlaceholderRegex()
    {
        return '/'
        . '(' . preg_quote($this->container->getParameter('wizin_simple_cms.left_delimiter'), '/') . ')'
        . '(\s*)(\S+)(\s*)'
        . '(' . preg_quote($this->container->getParameter('wizin_simple_cms.right_delimiter'), '/') . ')'
        . '/';
    }

    /**
     * @param $templateFile
     * @return null|string template source
     */
    protected function getTemplateSource($templateFile)
    {
        if ($this->isExists($templateFile)) {
            return file_get_contents($this->getTemplateFilePath($templateFile));
        } else {
            return null;
        }
    }

    /**
     * @param string $source source before replace
     * @param array $parameters
     * @return string $source source after replace
     */
    protected function replaceSource($source, array $parameters)
    {
        $pattern = $this->getPlaceholderRegex();
        preg_match_all($pattern, $source, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $key = $match[static::PLACEHOLDER_INDEX];
            if (isset($parameters[$key])) {
                $value = $parameters[$key];
            } else {
                $value = '';
            }
            $source = str_replace(
                $match[0],
                $value,
                $source
            );
        }

        return $source;
    }

    /**
     * @param Content $content
     * @return string cache file path
     */
    protected function getCachePath(Content $content)
    {
        return $this->container->get('kernel')->getCacheDir() . '/'
        . static::CACHE_DIR_NAME . '/' .$content->getId() . '.html.twig';
    }
}