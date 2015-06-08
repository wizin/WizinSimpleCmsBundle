<?php
namespace Wizin\Bundle\SimpleCmsBundle\Tests\Service;

use Wizin\Bundle\BaseBundle\TestCase\ServiceTestCase;
use Wizin\Bundle\SimpleCmsBundle\Entity\Content;

class ContentManagerTest extends ServiceTestCase
{
    /**
     * @return null
     */
    public function setUp()
    {
        parent::setUp();
        $this->truncate();
        $this->getEntityManager()->clear();
    }

    /**
     * @test
     */
    public function isValidService()
    {
        $this->assertInstanceOf('\Wizin\Bundle\SimpleCmsBundle\Service\ContentManager', $this->getService());
    }

    /**
     * @test
     * @dataProvider saveProvider
     */
    public function save(Content $content, $isDuplicated)
    {
        $repository = $this
            ->getMockBuilder('\Wizin\Bundle\SimpleCmsBundle\Repository\ContentRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $repository->expects($this->any())
            ->method('isDuplicated')
            ->will($this->returnValue($isDuplicated));
        $loader = $this
            ->getMockBuilder('\Wizin\Bundle\SimpleCmsBundle\Service\ClassLoader')
            ->disableOriginalConstructor()
            ->getMock();
        $loader->expects($this->any())
            ->method('getContentRepository')
            ->will($this->returnValue($repository));
        $this->getContainer()->set('wizin_simple_cms.class_loader', $loader);
        if ($isDuplicated) {
            $this->setExpectedException('Wizin\Bundle\SimpleCmsBundle\Exception\DuplicateContentException');
            $this->getService()->save($content);
        } else {
            $this->getService()->save($content);
            $repository = $this->getEntityManager()->getRepository('WizinSimpleCmsBundle:Content');
            $result = $repository->find($content->getId());
            $this->assertInstanceOf('Wizin\Bundle\SimpleCmsBundle\Entity\Content', $result);
            $this->assertNotNull($result->getId());
        }
    }

    /**
     * @return array
     */
    public function saveProvider()
    {
        $data = [];
        $content = (new Content())
            ->setPathInfo('/cms/front/new_page')
            ->setTitle('new page')
            ->setParameters(['body' => 'new page'])
            ->setTemplateFile('default.html.twig')
            ->setActive(true)
        ;
        $data[] = [$content, false];
        $content = (new Content())
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
     * @return \Wizin\Bundle\SimpleCmsBundle\Service\ContentManager
     */
    protected function getService()
    {
        return $this->getContainer()->get('wizin_simple_cms.content_manager');
    }
}
