<?php

namespace Wizin\Bundle\SimpleCmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Wizin\Bundle\SimpleCmsBundle\Traits\ControllerTrait;

/**
 * Class FrontController
 * @package Wizin\Bundle\SimpleCmsBundle\Controller
 */
class FrontController extends Controller
{
    /**
     * \Wizin\Bundle\SimpleCmsBundle\Traits\ControllerTrait
     */
    use ControllerTrait;

    /**
     * @Route(
     *   "/{path}",
     *   name="wizin_simple_cms_front_show",
     *   requirements={"path"=".*"},
     *   defaults={"path"=""}
     * )
     */
    public function showAction($path)
    {
        $template = $this->getTemplateService();
        $pathInfo = $this->getRequest()->getPathInfo();
        // retrieve Content instance by $pathInfo
        $content = $this->getContentRepository()->retrieveEnableContent($pathInfo);
        if (is_null($content)) {
            // invalid url
            throw new NotFoundHttpException();
        }
        // create response
        $response = new Response();
        $responseContent = $template->generateResponseContent($content);
        $response->setContent($responseContent);

        return $response;
    }
}
