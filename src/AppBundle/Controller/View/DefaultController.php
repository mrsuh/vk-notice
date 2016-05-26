<?php

namespace AppBundle\Controller\View;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('AppBundle:Default:index.html.twig', [
            'subways' => $this->get('model.notify')->getSubways()
        ]);
    }

    public function unsubscribeAction($hash_id)
    {
        $id = $this->get('hash.unsubscribe')->decodeSingle($hash_id);
        $email = $this->get('model.notify')->getSubscribedEmailById($id);
        return $this->render('AppBundle:Default:unsubscribe.html.twig',
            [
                'email' => $email,
                'hash_id' => $hash_id
            ]
        );
    }
}
