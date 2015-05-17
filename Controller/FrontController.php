<?php

namespace Wizin\Bundle\SimpleCmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class FrontController
 * @package Wizin\Bundle\SimpleCmsBundle\Controller
 */
class FrontController extends Controller
{
    /**
     * @Route("/show")
     */
    public function showAction()
    {
        /** @var \Wizin\Bundle\SimpleCmsBundle\Service\Template $template */
        $template = $this->get('wizin_simple_cms.template');
        $pathInfo = $this->getRequest()->getPathInfo();
        // TODO: retrieve Content instance by $pathInfo
        $content = (new \Wizin\Bundle\SimpleCmsBundle\Entity\Content())
            ->setPathInfo($pathInfo)
            ->setTitle('dummy')
            ->setParameters(['body' => '<h1>Test</h1>'])
            ->setTemplate('default.html.twig')
        ;
        // create response
        $response = new Response();
        $responseContent = $template->generateResponseContent($content);
        $response->setContent($responseContent);

        return $response;
    }
}
