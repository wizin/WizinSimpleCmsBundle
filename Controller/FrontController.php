<?php

namespace Wizin\Bundle\SimpleCmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class FrontController extends Controller
{
    public function showAction()
    {
        /** @var \Twig_Environment $twig */
        $twig = $this->get('twig');
        $twig->addExtension(new \Twig_Extension_StringLoader());
        // TODO: retrieve $title from database
        $title = 'dummy';
        // TODO: retrieve $templateDirectory from config
        // TODO: retrieve $template from database
        $templateDirectory = __DIR__ .'/../Resources/templates';
        $template = 'default.html.twig';
        $body = $twig->render(
            realpath($templateDirectory . '/' .$template),
            [
                'title' => $title,
            ]
        );
        // TODO: retrieve $content from database
        $content = [
            'subTitle' => '<h1>Test</h1>',
        ];
        foreach ($content as $key => $value) {
            $body = str_replace(
                $this->container->getParameter('wizin_simple_cms.left_delimiter') . $key . $this->container->getParameter('wizin_simple_cms.right_delimiter'),
                $value,
                $body
            );
        }
        $response = new Response();
        $response->setContent($body);

        return $response;
    }
}
