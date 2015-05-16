<?php

namespace Wizin\Bundle\SimpleCmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class FrontController extends Controller
{
    public function showAction()
    {
        /** @var \Wizin\Bundle\SimpleCmsBundle\Service\Template $template */
        $template = $this->get('wizin_simple_cms.template');
        // TODO: retrieve $template from database
        $templateFile = 'default.html.twig';
        // TODO: retrieve $title from database
        $title = 'dummy';
        // TODO: retrieve $parameters from database
        $parameters = [
            'subTitle' => '<h1>Test</h1>',
        ];
        // generate body string
        $body = $this->renderView(
            realpath($template->getTemplateDir() . '/' .$templateFile),
            [
                'title' => $title,
            ]
        );
        foreach ($parameters as $key => $value) {
            $body = str_replace(
                $this->container->getParameter('wizin_simple_cms.left_delimiter')
                    . $key . $this->container->getParameter('wizin_simple_cms.right_delimiter'),
                $value,
                $body
            );
        }
        // create response
        $response = new Response();
        $response->setContent($body);

        return $response;
    }
}
