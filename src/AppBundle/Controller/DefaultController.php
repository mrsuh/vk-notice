<?php

namespace AppBundle\Controller;

use AppBundle\C;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('AppBundle:Default:index.html.twig', [
            'subways' => $this->get('doctrine.orm.entity_manager')->getRepository(C::REPO_SUBWAY)->findAll()
        ]);
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
