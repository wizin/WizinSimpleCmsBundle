<?php
namespace Wizin\Bundle\SimpleCmsBundle\Tests\Service;

use Symfony\Component\HttpFoundation\Request;
use Wizin\Bundle\BaseBundle\TestCase\ServiceTestCase;
use Wizin\Bundle\SimpleCmsBundle\Entity\Content;

class TemplateTest extends ServiceTestCase
{
    /**
     * @test
     */
    public function isValidService()
    {
        $this->assertInstanceOf('\Wizin\Bundle\SimpleCmsBundle\Service\Template', $this->getService());
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
                    'default.html.twig'
                ]
            ],
            [
                dirname(__DIR__) . '/Resources/templates/',
                [
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
     * @dataProvider generateResponseContentProvider
     */
    public function generateResponseContent(Content $content, $expected)
    {
        $this->getContainer()->enterScope('request');
        $this->getContainer()->set('request', new Request(), 'request');
        $responseContent = $this->getService()->generateResponseContent($content);
        $this->assertContains('<title>' .$expected['title'] .'</title>', $responseContent);
        $this->assertContains($expected['body'], $responseContent);
    }

    /**
     * data provider for $this->generateResponseContent()
     *
     * @return array
     */
    public function generateResponseContentProvider()
    {
        $data = [];
        $title = 'test page';
        $body = '<h1>Test</h1>';
        $testContent = (new \Wizin\Bundle\SimpleCmsBundle\Entity\Content())
            ->setPathInfo('/test')
            ->setTitle($title)
            ->setParameters(['body' => $body])
            ->setTemplate('default.html.twig')
        ;
        $data[] = [$testContent, ['title' => $title, 'body' => $body]];
        $title = 'dummy page';
        $body = '<h1>Dummy</h1>';
        $dummyContent = (new \Wizin\Bundle\SimpleCmsBundle\Entity\Content())
            ->setPathInfo('/dummy')
            ->setTitle($title)
            ->setParameters(['body' => $body])
            ->setTemplate('default.html.twig')
        ;
        $data[] = [$dummyContent, ['title' => $title, 'body' => $body]];

        return $data;
    }

    /**
     * @return \Wizin\Bundle\SimpleCmsBundle\Service\Template
     */
    protected function getService()
    {
        return $this->getContainer()->get('wizin_simple_cms.template');
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
