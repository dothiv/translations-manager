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
        $this->prepareChanges();
        return new Response(json_encode($git->diff()));
    }

    public function commitAction()
    {
        $git = $this->get('git');
        $this->prepareChanges();
        $data = json_decode($this->getRequest()->getContent());
        $git->commit($data->msg, $data->name, $data->email);
        return new Response(json_encode($git->log()));
    }

    public function downloadAction($locale)
    {
        return new Response($this->get('git')->cat($this->getLangFilePath($locale)), 200, array(
                    'Content-Type' => 'text/json',
                    'Content-Dispositon' => 'attachment; filename="language-'.$locale.'.json";',
                ));
    }

    private function prepareChanges() 
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

        // prepare the changes
        $git->discard();
        foreach($data->files as $locale => $file) {
            $git->change($this->getLangFilePath($locale), $file);
        }
    }

    private function getLangFilePath($locale)
    {
        return 'src/DotHiv/WebsiteCharityBundle/Resources/public/translations/language-'. $locale .'.json';
    }
}
