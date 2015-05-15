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
     * @return \Wizin\Bundle\SimpleCmsBundle\Service\Template
     */
    protected function getService()
    {
        return $this->getContainer()->get('wizin_simple_cms.template');
    }

}
