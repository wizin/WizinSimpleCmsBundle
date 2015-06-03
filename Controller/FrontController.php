<?php

namespace Wizin\Bundle\SimpleCmsBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class FrontController
 * @package Wizin\Bundle\SimpleCmsBundle\Controller
 */
class FrontController extends Controller
{
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
        $pathInfo = $this->getRequest()->getPathInfo();
        // retrieve Content instance by $pathInfo
        $content = $this->getContentManager()->getContentRepository()->retrieveEnableContent($pathInfo);
        if (is_null($content)) {
            // invalid url
            throw new NotFoundHttpException();
        }
        // create response
        $response = new Response();
        $responseContent = $this->getTemplateHandler()->generateResponseContent($content);
        $response->setContent($responseContent);

        return $response;
    }
}
