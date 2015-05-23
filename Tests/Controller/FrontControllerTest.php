<?php

namespace Wizin\Bundle\SimpleCmsBundle\Tests\Controller;

use Wizin\Bundle\BaseBundle\TestCase\FunctionalTestCase;
use Wizin\Bundle\SimpleCmsBundle\DataFixtures\ORM\ContentFixtureLoader;

class FrontControllerTest extends FunctionalTestCase
{
    /**
     * @return null
     */
    public function setUp()
    {
        parent::setUp();
        $this->loadFixtures(
            [
                new ContentFixtureLoader(),
            ]
        );
    }

    /**
     * @test
     * @dataProvider showActionProvider
     * @param string $uri
     * @param bool $isSuccessful
     * @param string $status
     * @param string $title
     */
    public function showAction($uri, $isSuccessful, $status, $title)
    {
        $crawler = $this->client->request('GET', $uri);
        $this->assertEquals($isSuccessful, $this->client->getResponse()->isSuccessful());
        $this->assertEquals($status, $this->client->getResponse()->getStatusCode());
        if ($isSuccessful) {
            $this->assertEquals($title, $crawler->filter('title')->text());
        } else {
        }
    }

    public function showActionProvider()
    {
        $data = [];
        $data[] = ['/cms/front/page1', true, '200', 'Test Page 1'];
        $data[] = ['/cms/front/not_found', false, '404', null];

        return $data;
    }
}
