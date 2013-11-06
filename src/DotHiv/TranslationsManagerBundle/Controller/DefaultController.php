<?php

namespace DotHiv\TranslationsManagerBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('DotHivTranslationsManagerBundle:Default:index.html.twig');
    }

//     public function getCsvAction($key)
//     {
//         $url = 'https://docs.google.com/spreadsheet/ccc?key=' . $key . '&output=csv&hl&pref=2';
//         return new Response($url);
//         $ch = curl_init($url);
//         $ckfile = tempnam("/tmp", "curlcookie");
//         curl_setopt ($ch, CURLOPT_COOKIEJAR, $ckfile);
//         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//         curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
//         $csv = curl_exec($ch);
//         unlink($ckfile);
//         return new Response($csv);
//     }
}
