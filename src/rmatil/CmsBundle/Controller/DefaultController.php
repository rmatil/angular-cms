<?php

namespace rmatil\CmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('rmatilCmsBundle:Default:index.html.twig');
    }
}
