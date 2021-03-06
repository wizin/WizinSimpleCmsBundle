<?php
namespace Wizin\Bundle\SimpleCmsBundle\Tests\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Filesystem\Filesystem;
use Wizin\Bundle\BaseBundle\TestCase\ServiceTestCase;
use Wizin\Bundle\SimpleCmsBundle\Entity\Content;

class TemplateHandlerTest extends ServiceTestCase
{
    /**
     * @test
     */
    public function isValidService()
    {
        $this->assertInstanceOf('\Wizin\Bundle\SimpleCmsBundle\Service\TemplateHandler', $this->getService());
    }

    /**
     * @test
     * @dataProvider getTemplateFilesProvider
     */
    public function getTemplateFiles($templateDir, $expected)
    {
        $service = $this->getService();
        $service->setTemplateDir($templateDir);
        $templateFiles = $this->getService()->getTemplateFiles();
        $this->assertTrue(is_array($templateFiles));
        $this->assertSame($expected, $templateFiles);
    }

    /**
     * data provider for $this->getTemplateFiles()
     *
     * @return array
     */
    public function getTemplateFilesProvider()
    {
        return [
            [
                null,
                [
                    'default.html.twig',
                    'free_format.html.twig'
                ]
            ],
            [
                dirname(__DIR__) . '/Resources/templates/',
                [
                    'default.html.twig',
                    'dir/test.html.twig',
                    'test.html.twig',
                ]
            ]
        ];
    }

    /**
     * @test
     */
    public function getTemplateFilePath()
    {
        $templateFile = 'default.html.twig';
        $this->assertEquals($this->getTestFilePath(), $this->getService()->getTemplateFilePath($templateFile));
    }

    /**
     * @test
     * @dataProvider isExistsProvider
     */
    public function isExists($templateFile, $expected)
    {
        $this->assertSame($expected, $this->getService()->isExists($templateFile));
    }

    /**
     * data provider for $this->isExists()
     *
     * @return array
     */
    public function isExistsProvider()
    {
        return [
            ['default.html.twig', true],
            ['test.html.twig', false],
        ];
    }

    /**
     * @test
     */
    public function getPlaceholders()
    {
        $this->assertSame(['body'], $this->getService()->getPlaceholders('default.html.twig'));
    }

    /**
     * @test
     */
    public function getOptions()
    {
        $templateDir = dirname(__DIR__) . '/Resources/templates/';
        $service = $this->getService();
        $service->setTemplateDir($templateDir);
        $expected = [
            'header' => ['label' => ['label' => 'Header Block']],
            'footer' => ['label' => ['label' => 'Footer Block']],
        ];
        $this->assertSame($expected, $this->getService()->getOptions('test.html.twig'));
    }

    /**
     * @test
     * @dataProvider removeCacheProvider
     */
    public function removeCache(Content $content, $expected)
    {
        $service = $this->getService();
        $filesystem = new Filesystem();
        $cache = static::$kernel->getCacheDir() . '/' .  $service::CACHE_DIR_NAME . '/' .$content->getId() . '.' .$expected['suffix'] .'.html.twig';
        $filesystem->dumpFile($cache, '');
        $this->assertTrue($filesystem->exists($cache));
        $service->removeCache($content);
        $this->assertFalse($filesystem->exists($cache));
    }

    /**
     * data provider for $this->removeCache()
     *
     * @return array
     */
    public function removeCacheProvider()
    {
        $data = [];
        $title = 'test page';
        $body = '<h1>Test</h1>';
        $testContent = (new \Wizin\Bundle\SimpleCmsBundle\Entity\Content())
            ->setId('00000000-0000-0000-0000-000000000001')
            ->setPathInfo('/test')
            ->setTitle($title)
            ->setParameters(['body' => $body])
            ->setTemplateFile('default.html.twig')
        ;
        $seed = $testContent->getId() . $testContent->getPathInfo() . $testContent->getTitle()
            . $testContent->getTemplateFile() . json_encode($testContent->getParameters());
        if (function_exists('hash')) {
            $suffix = hash('sha256', $seed);
        } else {
            $suffix = sha1($seed);
        }
        $data[] = [$testContent, ['suffix' => $suffix]];
        $title = 'dummy page';
        $body = '<h1>Dummy</h1>';
        $dummyContent = (new \Wizin\Bundle\SimpleCmsBundle\Entity\Content())
            ->setId('00000000-0000-0000-0000-000000000002')
            ->setPathInfo('/dummy')
            ->setTitle($title)
            ->setParameters(['body' => $body])
            ->setTemplateFile('default.html.twig')
        ;
        $seed = $dummyContent->getId() . $dummyContent->getPathInfo() . $dummyContent->getTitle()
            . $dummyContent->getTemplateFile() . json_encode($dummyContent->getParameters());
        if (function_exists('hash')) {
            $suffix = hash('sha256', $seed);
        } else {
            $suffix = sha1($seed);
        }
        $data[] = [$dummyContent, ['suffix' => $suffix]];

        return $data;
    }

    /**
     * @test
     * @dataProvider getTemplateCacheProvider
     */
    public function getTemplateCache(Content $content, $expected)
    {
        $service = $this->getService();
        $filesystem = new Filesystem();
        $cache = static::$kernel->getCacheDir() . '/' .  $service::CACHE_DIR_NAME . '/' .$content->getId() . '.' .$expected['suffix'] .'.html.twig';
        $this->assertFalse($filesystem->exists($cache));
        $templateCache = $service->getTemplateCache($content);
        $this->assertEquals($cache, $templateCache);
        $this->assertTrue($filesystem->exists($cache));
    }

    /**
     * data provider for $this->getTemplateCache()
     *
     * @return array
     */
    public function getTemplateCacheProvider()
    {
        $data = [];
        $title = 'test page';
        $body = '<h1>Test</h1>';
        $testContent = (new \Wizin\Bundle\SimpleCmsBundle\Entity\Content())
            ->setId('00000000-0000-0000-0000-000000000001')
            ->setPathInfo('/test')
            ->setTitle($title)
            ->setParameters(['body' => $body])
            ->setTemplateFile('default.html.twig')
        ;
        $seed = $testContent->getId() . $testContent->getPathInfo() . $testContent->getTitle()
            . $testContent->getTemplateFile() . json_encode($testContent->getParameters());
        if (function_exists('hash')) {
            $suffix = hash('sha256', $seed);
        } else {
            $suffix = sha1($seed);
        }
        $data[] = [$testContent, ['suffix' => $suffix]];
        $title = 'dummy page';
        $body = '<h1>Dummy</h1>';
        $dummyContent = (new \Wizin\Bundle\SimpleCmsBundle\Entity\Content())
            ->setId('00000000-0000-0000-0000-000000000002')
            ->setPathInfo('/dummy')
            ->setTitle($title)
            ->setParameters(['body' => $body])
            ->setTemplateFile('default.html.twig')
        ;
        $seed = $dummyContent->getId() . $dummyContent->getPathInfo() . $dummyContent->getTitle()
            . $dummyContent->getTemplateFile() . json_encode($dummyContent->getParameters());
        if (function_exists('hash')) {
            $suffix = hash('sha256', $seed);
        } else {
            $suffix = sha1($seed);
        }
        $data[] = [$dummyContent, ['suffix' => $suffix]];

        return $data;
    }

    /**
     * @return null
     */
    public function tearDown()
    {
        $service = $this->getService();
        $filesystem = new Filesystem();
        $filesystem->remove(static::$kernel->getCacheDir() . '/' .  $service::CACHE_DIR_NAME);

        parent::tearDown();
    }

    /**
     * @return \Wizin\Bundle\SimpleCmsBundle\Service\TemplateHandler
     */
    protected function getService()
    {
        return $this->getContainer()->get('wizin_simple_cms.template_handler');
    }

    /**
     * @param string $templateFile
     * @return string path of test file
     */
    private function getTestFilePath($templateFile = 'default.html.twig')
    {
        return dirname(dirname(__DIR__)) . '/Resources/templates/' .$templateFile;
    }
}
