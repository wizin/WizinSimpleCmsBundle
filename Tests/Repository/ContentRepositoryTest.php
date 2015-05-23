<?php
namespace Wizin\Bundle\SimpleCmsBundle\Tests\Repository;

use Wizin\Bundle\BaseBundle\TestCase\RepositoryTestCase;
use Wizin\Bundle\SimpleCmsBundle\DataFixtures\ORM\ContentFixtureLoader;
use Wizin\Bundle\SimpleCmsBundle\Entity\Content;

class ContentRepositoryTest extends RepositoryTestCase
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
     * @dataProvider retrieveEnableContentProvider
     */
    public function retrieveEnableContent($pathInfo, $class, $properties)
    {
        $content = $this->getRepository()->retrieveEnableContent($pathInfo);
        if (is_null($class)) {
            $this->assertNull($content);
        } else {
            $this->assertInstanceOf($class, $content);
            foreach ($properties as $property => $value) {
                $getter = 'get' .ucfirst($property);
                $this->assertEquals($value, $content->$getter());
            }
        }
    }

    public function retrieveEnableContentProvider()
    {
        $data = [];
        $data[] = [
            '/cms/front/page1',
            '\Wizin\Bundle\SimpleCmsBundle\Entity\Content',
            ['id' => '00000000-0000-0000-0000-000000000001', 'title' => 'Test Page 1']
        ];
        $data[] = ['/cms/front/page2', null, null];
        $data[] = ['/cms/front/page3', null, null];

        return $data;
    }

    /**
     * @test
     * @dataProvider isDuplicatedProvider
     */
    public function isDuplicated($content, $expected)
    {
        $this->assertSame($expected, $this->getRepository()->isDuplicated($content));
    }

    public function isDuplicatedProvider()
    {
        $data = [];
        $content = (new Content())
            ->setPathInfo('/cms/front/page1')
            ->setTitle('new page')
            ->setParameters(['body' => 'new page'])
            ->setTemplateFile('default.html.twig')
            ->setActive(true)
        ;
        $data[] = [$content, true];
        $content = (new Content())
            ->setPathInfo('/cms/front/new_page')
            ->setTitle('new page')
            ->setParameters(['body' => 'new page'])
            ->setTemplateFile('default.html.twig')
            ->setActive(true)
        ;
        $data[] = [$content, false];
        $content = (new Content())
            ->setId('00000000-0000-0000-0000-000000000001')
            ->setPathInfo('/cms/front/page1')
            ->setTitle('exist page')
            ->setParameters(['body' => 'exist page'])
            ->setTemplateFile('default.html.twig')
            ->setActive(false)
        ;
        $data[] = [$content, false];
        $content = (new Content())
            ->setId('00000000-0000-0000-0000-999999999999')
            ->setPathInfo('/cms/front/page1')
            ->setTitle('exist page')
            ->setParameters(['body' => 'exist page'])
            ->setTemplateFile('default.html.twig')
            ->setActive(false)
        ;
        $data[] = [$content, true];

        return $data;
    }


    /**
     * @return \Wizin\Bundle\SimpleCmsBundle\Repository\ContentRepository
     */
    protected function getRepository()
    {
        return $this->getEntityManager()->getRepository('WizinSimpleCmsBundle:Content');
    }
}

