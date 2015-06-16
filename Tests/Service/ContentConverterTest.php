<?php
namespace Wizin\Bundle\SimpleCmsBundle\Tests\Service;

use Wizin\Bundle\BaseBundle\TestCase\ServiceTestCase;
use Wizin\Bundle\SimpleCmsBundle\Entity\Content;
use Wizin\Bundle\SimpleCmsBundle\Entity\DraftContent;

class ContentConverterTest extends ServiceTestCase
{
    /**
     * @test
     */
    public function isValidService()
    {
        $this->assertInstanceOf('\Wizin\Bundle\SimpleCmsBundle\Service\ContentConverter', $this->getService());
    }

    /**
     * @test
     * @dataProvider convertToDraftProvider
     */
    public function convertToDraft(Content $content, $draftContent)
    {
        // set mock
        $repository = $this
            ->getMockBuilder('\Wizin\Bundle\SimpleCmsBundle\Repository\DraftContentRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $repository->expects($this->any())
            ->method('findOneBy')
            ->will($this->returnValue($draftContent));
        $repository->expects($this->any())
            ->method('getClassName')
            ->will($this->returnValue('\Wizin\Bundle\SimpleCmsBundle\Entity\DraftContent'));
        $loader = $this
            ->getMockBuilder('\Wizin\Bundle\SimpleCmsBundle\Service\ClassLoader')
            ->disableOriginalConstructor()
            ->getMock();
        $loader->expects($this->any())
            ->method('getDraftContentRepository')
            ->will($this->returnValue($repository));
        $this->getContainer()->set('wizin_simple_cms.class_loader', $loader);
        // execute test
        $draft = $this->getService()->convertToDraft($content);
        $this->assertInstanceOf('\Wizin\Bundle\SimpleCmsBundle\Entity\DraftContent', $draft);
        $this->assertSame($content->getId(), $draft->getContentId());
        $this->assertSame($content->getPathInfo(), $draft->getPathInfo());
        $this->assertSame($content->getTitle(), $draft->getTitle());
        $this->assertSame($content->getTemplateFile(), $draft->getTemplateFile());
        $this->assertSame($content->getParameters(), $draft->getParameters());
        $this->assertSame($content->getActive(), $draft->getActive());
    }

    /**
     * @return array
     */
    public function convertToDraftProvider()
    {
        $data = [];
        $content = (new Content())
            ->setId('00000000-0000-0000-0000-000000000001')
            ->setPathInfo('/cms/front/update_page')
            ->setTitle('update page')
            ->setParameters(['body' => 'update page'])
            ->setTemplateFile('free_format.html.twig')
            ->setActive(false)
        ;
        $data[] = [$content, null];
        $content = (new Content())
            ->setId('00000000-0000-0000-0000-000000000002')
            ->setPathInfo('/cms/front/exist_page')
            ->setTitle('exist page')
            ->setParameters(['body' => 'exist page'])
            ->setTemplateFile('default.html.twig')
            ->setActive(true)
        ;
        $draftContent = (new DraftContent())
            ->setContentId('00000000-0000-0000-0000-000000000002')
            ->setPathInfo('/cms/front/draft_page')
            ->setTitle('draft page')
            ->setParameters(['body' => 'draft page'])
            ->setTemplateFile('default.html.twig')
            ->setActive(true)
        ;
        $data[] = [$content, $draftContent];

        return $data;
    }

    /**
     * @test
     * @dataProvider convertFromDraftProvider
     */
    public function convertFromDraft(DraftContent $draftContent, $content)
    {
        // set mock
        $repository = $this
            ->getMockBuilder('\Wizin\Bundle\SimpleCmsBundle\Repository\ContentRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $repository->expects($this->any())
            ->method('find')
            ->will($this->returnValue($content));
        $loader = $this
            ->getMockBuilder('\Wizin\Bundle\SimpleCmsBundle\Service\ClassLoader')
            ->disableOriginalConstructor()
            ->getMock();
        $loader->expects($this->any())
            ->method('getContentRepository')
            ->will($this->returnValue($repository));
        $this->getContainer()->set('wizin_simple_cms.class_loader', $loader);
        // execute test
        if (is_null($content)) {
            $this->setExpectedException('\Wizin\Bundle\SimpleCmsBundle\Exception\OrphanDraftException');
        }
        $result = $this->getService()->convertFromDraft($draftContent);
        $this->assertInstanceOf('\Wizin\Bundle\SimpleCmsBundle\Entity\Content', $result);
        $this->assertSame($draftContent->getPathInfo(), $result->getPathInfo());
        $this->assertSame($draftContent->getTitle(), $result->getTitle());
        $this->assertSame($draftContent->getTemplateFile(), $result->getTemplateFile());
        $this->assertSame($draftContent->getParameters(), $result->getParameters());
        $this->assertSame($draftContent->getActive(), $result->getActive());
    }

    /**
     * @return array
     */
    public function convertFromDraftProvider()
    {
        $data = [];
        $draftContent = (new DraftContent())
            ->setContentId('00000000-0000-0000-0000-000000000002')
            ->setPathInfo('/cms/front/draft_page')
            ->setTitle('draft page')
            ->setParameters(['body' => 'draft page'])
            ->setTemplateFile('default.html.twig')
            ->setActive(true)
        ;
        $content = (new Content())
            ->setId('00000000-0000-0000-0000-000000000002')
            ->setPathInfo('/cms/front/exist_page')
            ->setTitle('exist page')
            ->setParameters(['body' => 'exist page'])
            ->setTemplateFile('default.html.twig')
            ->setActive(true)
        ;
        $data[] = [$draftContent, $content];
        $draftContent = (new DraftContent())
            ->setContentId('00000000-0000-0000-0000-000000000000')
            ->setPathInfo('/cms/front/orphan_draft')
            ->setTitle('orphan draft page')
            ->setParameters(['body' => 'draft page'])
            ->setTemplateFile('default.html.twig')
            ->setActive(true)
        ;
        $content = null;
        $data[] = [$draftContent, $content];

        return $data;
    }

    /**
     * @return \Wizin\Bundle\SimpleCmsBundle\Service\ContentConverter
     */
    protected function getService()
    {
        return $this->getContainer()->get('wizin_simple_cms.content_converter');
    }
}

