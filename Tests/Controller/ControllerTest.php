<?php

namespace Wizin\Bundle\SimpleCmsBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\Request;
use Wizin\Bundle\BaseBundle\TestCase\FunctionalTestCase;
use Wizin\Bundle\SimpleCmsBundle\Controller\Controller;
use Wizin\Bundle\SimpleCmsBundle\Entity\ContentInterface;
use Wizin\Bundle\SimpleCmsBundle\Entity\Content;
use Wizin\Bundle\SimpleCmsBundle\Event\Event;
use Wizin\Bundle\SimpleCmsBundle\Event\InjectVariablesEvent;
use Wizin\Bundle\SimpleCmsBundle\Service\TemplateHandler;

class ControllerTest extends FunctionalTestCase
{
    /**
     * @test
     * @dataProvider sendContentProvider
     */
    public function sendContent(ContentInterface $content, array $variables)
    {
        // set event
        $dispatcher = $this->getContainer()->get('event_dispatcher');
        $dispatcher->addListener(
            Event::ON_INJECT_VARIABLES,
            function (InjectVariablesEvent $event) use ($variables) {
                $event->setVariables($variables);
            }
        );
        // set mock
        $templateHandler = new TemplateHandler();
        $templateHandler->setContainer($this->getContainer());
        $templateHandler->setTemplateDir(dirname(__DIR__) .'/Resources/templates');
        $this->getContainer()->set('wizin_simple_cms.template_handler', $templateHandler);
        $this->getContainer()->enterScope('request');
        $this->getContainer()->set('request', new Request(), 'request');
        $controller = $this->getController();
        $method = new \ReflectionMethod(get_class($controller), 'sendContent');
        $method->setAccessible(true);

        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = $method->invoke($controller, $content);
        $this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);
        $this->assertContains($variables['header'], $response->getContent());
        $this->assertContains($variables['footer'], $response->getContent());
    }

    /**
     * @return array
     */
    public function sendContentProvider()
    {
        $data = [];
        $content = (new Content())
            ->setId('00000000-0000-0000-0000-dummy0000000')
            ->setPathInfo('/cms/front/default_page')
            ->setTitle('default page')
            ->setParameters(['body' => 'default page'])
            ->setTemplateFile('default.html.twig')
            ->setActive(true)
        ;
        $header = 'Header string';
        $footer = 'Footer string';
        $data[] = [$content, ['header' => $header, 'footer' => $footer]];
        $header = 'Header is empty';
        $footer = 'Footer is not empty';
        $data[] = [$content, ['header' => $header, 'footer' => $footer]];

        return $data;
    }

    /**
     * @return null
     */
    public function tearDown()
    {
        $this->getContainer()->leaveScope('request');

        parent::tearDown();
    }

    /**
     * @return \Wizin\Bundle\SimpleCmsBundle\Controller\Controller
     */
    protected function getController()
    {
        $controller = new Controller();
        $controller->setContainer($this->getContainer());

        return $controller;
    }
}
