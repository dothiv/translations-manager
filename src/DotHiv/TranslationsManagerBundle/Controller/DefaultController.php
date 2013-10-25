<?php

namespace DotHiv\TranslationsManagerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('DotHivTranslationsManagerBundle:Default:index.html.twig', array('name' => $name));
    }
}
