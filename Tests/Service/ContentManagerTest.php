<?php
namespace Wizin\Bundle\SimpleCmsBundle\Tests\Service;

use Wizin\Bundle\BaseBundle\TestCase\ServiceTestCase;

class ContentManagerTest extends ServiceTestCase
{
    /**
     * @test
     */
    public function isValidService()
    {
        $this->assertInstanceOf('\Wizin\Bundle\SimpleCmsBundle\Service\ContentManager', $this->getService());
    }

    /**
     * @return \Wizin\Bundle\SimpleCmsBundle\Service\ContentManager
     */
    protected function getService()
    {
        return $this->getContainer()->get('wizin_simple_cms.content_manager');
    }
}
