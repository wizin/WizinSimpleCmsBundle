<?php

namespace Wizin\Bundle\SimpleCmsBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
        // retrieve content instance by $pathInfo
        /** @var \Wizin\Bundle\SimpleCmsBundle\Entity\ContentInterface $content */
        $content = $this->getContentManager()->retrieveEnableContent($pathInfo);
        if (is_null($content)) {
            // invalid url
            throw new NotFoundHttpException();
        }

        return $this->sendContent($content);
    }
}
