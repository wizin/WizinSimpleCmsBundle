<?php
namespace Wizin\Bundle\SimpleCmsBundle\Tests\Service;

use Wizin\Bundle\BaseBundle\TestCase\ServiceTestCase;

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

    public function getTemplateFilesProvider()
    {
        return [
            [
                null,
                [
                    dirname(dirname(__DIR__)) . '/Resources/templates/default.html.twig'
                ]
            ],
            [
                dirname(__DIR__) . '/Resources/templates',
                [
                    dirname(__DIR__) . '/Resources/templates/test.html.twig',
                    dirname(__DIR__) . '/Resources/templates/test.txt.twig'
                ]
            ]
        ];
    }

    /**
     * @return \Wizin\Bundle\SimpleCmsBundle\Service\Template
     */
    protected function getService()
    {
        return $this->getContainer()->get('wizin_simple_cms.template');
    }

}
