<?php
namespace Wizin\Bundle\SimpleCmsBundle\Tests\Service;

use Wizin\Bundle\BaseBundle\TestCase\ServiceTestCase;
use Wizin\Bundle\SimpleCmsBundle\DataFixtures\ORM\ContentFixtureLoader;
use Wizin\Bundle\SimpleCmsBundle\Entity\Content;

class ContentManagerTest extends ServiceTestCase
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
     * @dataProvider saveContentProvider
     */
    public function saveContent(Content $content, $isDuplicated, $isNew)
    {
        $repository = $this->getEntityManager()->getRepository('WizinSimpleCmsBundle:Content');
        $draftContentRepository = $this->getEntityManager()->getRepository('WizinSimpleCmsBundle:DraftContent');
        if ($isNew === false) {
            $pathInfo = $content->getPathInfo();
            $title = $content->getTitle();
            /** @var \Wizin\Bundle\SimpleCmsBundle\Entity\Content $content */
            $content = $repository->find($content->getId());
            $content
                ->setPathInfo($pathInfo)
                ->setTitle($title)
            ;
        }
        // execute test
        if ($isDuplicated === true) {
            $this->setExpectedException('Wizin\Bundle\SimpleCmsBundle\Exception\DuplicateContentException');
        }
        $this->getService()->save($content);
        $this->getEntityManager()->clear();
        /** @var \Wizin\Bundle\SimpleCmsBundle\Entity\Content $result */
        $result = $repository->find($content->getId());
        $this->assertInstanceOf('Wizin\Bundle\SimpleCmsBundle\Entity\Content', $result);
        $this->assertNotNull($result->getId());
        $this->assertSame($result->getPathInfo(), $content->getPathInfo());
        $this->assertSame($result->getTitle(), $content->getTitle());
        $this->assertSame($result->getTemplateFile(), $content->getTemplateFile());
        $this->assertSame($result->getParameters(), $content->getParameters());
        $this->assertSame($result->getActive(), $content->getActive());
        /** @var \Wizin\Bundle\SimpleCmsBundle\Entity\DraftContent $draftResult */
        $draftResult = $draftContentRepository->findOneBy(['contentId' => $content->getId()]);
        $this->assertNull($draftResult);
    }

    /**
     * @return array
     */
    public function saveContentProvider()
    {
        $data = [];
        $content = (new Content())
            ->setPathInfo('/cms/front/new_page')
            ->setTitle('new page')
            ->setParameters(['body' => 'new page'])
            ->setTemplateFile('default.html.twig')
            ->setActive(true)
        ;
        $isDuplicated = false;
        $isNew = true;
        $data[] = [$content, $isDuplicated, $isNew];
        $content = (new Content())
            ->setPathInfo('/cms/front/page1')
            ->setTitle('exist page')
            ->setParameters(['body' => 'exist page'])
            ->setTemplateFile('default.html.twig')
            ->setActive(false)
        ;
        $isDuplicated = true;
        $isNew = true;
        $data[] = [$content, $isDuplicated, $isNew];
        $content = (new Content())
            ->setId('00000000-0000-0000-0000-000000000002')
            ->setPathInfo('/cms/front/page2_update')
            ->setTitle('exist page update')
        ;
        $isDuplicated = false;
        $isNew = false;
        $data[] = [$content, $isDuplicated, $isNew];

        return $data;
    }

    /**
     * @test
     * @dataProvider saveDraftProvider
     */
    public function saveDraft($contentId, $title, $active, $isDraft = null)
    {
        $contentRepository = $this->getEntityManager()->getRepository('WizinSimpleCmsBundle:Content');
        $draftContentRepository = $this->getEntityManager()->getRepository('WizinSimpleCmsBundle:DraftContent');
        /** @var \Wizin\Bundle\SimpleCmsBundle\Entity\Content $content */
        $content = $contentRepository->find($contentId);
        $content
            ->setTitle($title)
            ->setActive($active)
        ;
        // execute test
        if ($isDraft === null) {
            $this->getService()->save($content);
        } else {
            $this->getService()->save($content, $isDraft);
        }
        $this->getEntityManager()->clear();
        /** @var \Wizin\Bundle\SimpleCmsBundle\Entity\Content $contentResult */
        $contentResult = $contentRepository->find($content->getId());
        /** @var \Wizin\Bundle\SimpleCmsBundle\Entity\DraftContent $draftResult */
        $draftResult = $draftContentRepository->findOneBy(['contentId' => $content->getId()]);
        if ($isDraft === true) {
            $this->assertInstanceOf('Wizin\Bundle\SimpleCmsBundle\Entity\DraftContent', $draftResult);
            $this->assertNotNull($draftResult->getId());
            $this->assertSame($draftResult->getContentId(), $content->getId());
            $this->assertSame($draftResult->getPathInfo(), $content->getPathInfo());
            $this->assertSame($draftResult->getTitle(), $title);
            $this->assertSame($draftResult->getTemplateFile(), $content->getTemplateFile());
            $this->assertSame($draftResult->getParameters(), $content->getParameters());
            $this->assertSame($draftResult->getActive(), $active);
            $this->assertNotSame($contentResult->getTitle(), $title);
            $this->assertNotSame($contentResult->getActive(), $active);
        } else {
            $this->assertNull($draftResult);
            $this->assertSame($contentResult->getPathInfo(), $content->getPathInfo());
            $this->assertSame($contentResult->getTitle(), $title);
            $this->assertSame($contentResult->getTemplateFile(), $content->getTemplateFile());
            $this->assertSame($contentResult->getParameters(), $content->getParameters());
            $this->assertSame($contentResult->getActive(), $active);
        }
    }

    /**
     * @return array
     */
    public function saveDraftProvider()
    {
        $data = [];
        $contentId = '00000000-0000-0000-0000-000000000001';
        $title = 'form input title';
        $active = false;
        $isDraft = null;
        $data[] = [$contentId, $title, $active, $isDraft];
        $contentId = '00000000-0000-0000-0000-000000000001';
        $isDraft = false;
        $data[] = [$contentId, $title, $active, $isDraft];
        $contentId = '00000000-0000-0000-0000-000000000001';
        $isDraft = true;
        $data[] = [$contentId, $title, $active, $isDraft];

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
