<?php
/**
 * Template Service
 */
namespace Wizin\Bundle\SimpleCmsBundle\Service;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Wizin\Bundle\BaseBundle\Service\Service;
use Wizin\Bundle\SimpleCmsBundle\Entity\Content;

/**
 * Class Template
 * @package Wizin\Bundle\SimpleCmsBundle\Service
 */
class Template extends Service
{
    /**
     * regex index for placeholder
     */
    const PLACEHOLDER_INDEX = 3;

    /**
     * regex index for option
     */
    const OPTION_INDEX = 5;

    /**
     * cache directory name
     */
    const CACHE_DIR_NAME = 'cms';

    /**
     * @var string path of template directory
     */
    protected $templateDir;

    /**
     *
     */
    public function __construct()
    {
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
     * @param $templateFile
     * @return array options
     */
    public function getOptions($templateFile)
    {
        $options = [];
        $pattern = $this->getPlaceholderRegex();
        preg_match_all($pattern, $this->getTemplateSource($templateFile), $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $placeholder = $match[static::PLACEHOLDER_INDEX];
            $option = $match[static::OPTION_INDEX];
            if (strlen($option) > 0) {
                $option = trim($option);
            }
            if ($option !== '' && isset($options[$placeholder]) === false) {
                $option = json_decode($option, true);
                foreach($option as $key => & $value) {
                    if (is_array($value) === false) {
                        $value = [$key => $value];
                    }
                }
                $options[$placeholder] = $option;
            }
        }

        return $options;
    }

    /**
     * @param Content $content
     * @return string $responseContent content string for response
     */
    public function generateResponseContent(Content $content)
    {
        if ($this->container->get('kernel')->isDebug()) {
            $this->removeCache($content);
        }
        $filesystem = new Filesystem();
        $cachePath = $this->getCachePath($content);
        if ($filesystem->exists($cachePath) === false) {
            $source = $this->getTemplateSource($content->getTemplateFile());
            $source = $this->replaceSource($source, $content->getParameters());
            $filesystem->dumpFile($cachePath, $source);
        }
        $twig = $this->container->get('twig');
        $twig->getLoader()->addPath(dirname($cachePath));
        $responseContent = $twig->render(
            basename($cachePath),
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
        if ($filesystem->exists($this->getCacheDir())) {
            $finder = new Finder();
            $finder
                ->in($this->getCacheDir())
                ->files()
                ->followLinks()
                ->name($content->getId() . '.*.html.twig')
            ;
            foreach ($finder as $file) {
                /** @var \SplFileInfo $file */
                $filesystem->remove($file->getPathname());
            }
        }
    }

    /**
     * @return string regex pattern for placeholder
     */
    protected function getPlaceholderRegex()
    {
        return '/'
        . '(' . preg_quote($this->container->getParameter('wizin_simple_cms.left_delimiter'), '/') . ')'
        . '(\s*)(\S+)(\s*)(.*?)'
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
     * @return string cache directory path
     */
    protected function getCacheDir()
    {
        return $this->container->get('kernel')->getCacheDir() . '/' . static::CACHE_DIR_NAME;
    }

    /**
     * @param Content $content
     * @return string cache file path
     */
    protected function getCachePath(Content $content)
    {
        $seed = $content->getId() . $content->getPathInfo() . $content->getTitle()
            . $content->getTemplateFile() . json_encode($content->getParameters());
        if (function_exists('hash')) {
            $suffix = hash('sha256', $seed);
        } else {
            $suffix = sha1($seed);
        }
        return $this->getCacheDir() . '/' .$content->getId() . '.' .$suffix . '.html.twig';
    }
}