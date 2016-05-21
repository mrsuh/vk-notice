<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Parser;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $parser = new Parser();
        $un = $parser->parse(file_get_contents($this->get('kernel')->getRootDir(). '/fixtures/underground.yml') );

        return $this->render('AppBundle:Default:index.html.twig', ['undergounds' => $un]);
    }

    public function notifyAction(Request $request)
    {
        $email = $request->request->get('email');
        $link = $request->request->get('link');
        $needle = $request->request->get('needle');

        $model = $this->get('model.notify');
        if (
            !$model->isEmpty($email) ||
            !$model->isEmpty($link) ||
            !$model->isEmpty($needle) ||
            $model->isValidEmail($email) ||
            $model->isValidLink($link)
        ) {
            $status = 'ok';
            $model->register($email, $link, $needle);
        } else {
            $status = 'error';
        }

        return new JsonResponse(['status' => $status]);
    }
}
