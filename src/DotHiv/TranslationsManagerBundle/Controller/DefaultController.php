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

    public function diffAction()
    {
        $git = $this->get('git');

        // parse input
        $data = json_decode($this->getRequest()->getContent());

        // get the most recent updates
        try {
            $git->update();
        } catch (\Exception $e) {
            return new Response($e->getMessage(), 500);
        }

        // make the changes
        $git->discard();
        foreach($data->files as $locale => $file) {
            $git->change('src/DotHiv/WebsiteCharityBundle/Resources/public/translations/language-'. $locale .'.json', $file);
        }

        return new Response($git->diff());
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
