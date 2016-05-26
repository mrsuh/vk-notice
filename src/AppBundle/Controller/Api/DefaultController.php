<?php

namespace AppBundle\Controller\Api;

use AppBundle\Exception\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends FOSRestController
{
    public function subscribeAction(Request $request)
    {
        try {

            $this->get('model.notify')->subscribe(
                $request->request->get('email'),
                $request->request->get('city'),
                explode(',', $request->request->get('home_types')),
                explode(',', $request->request->get('subways'))
            );

            $response = ['status' => 'ok'];
        } catch (ValidationException $e) {
            $response = ['status' => 'err', 'data' => $e->getMessage()];
        } catch (\Exception $e) {
            $this->get('logger')->error($e->getMessage());
            $response = ['status' => 'err', 'data' => 'Internal error'];
        }

        return new JsonResponse($response);
    }

    public function unsubscribeAction(Request $request)
    {
        try {
            $id = $this->get('hash.unsubscribe')->decodeSingle($request->request->get('hash_id'));
            $email = $this->get('model.notify')->getSubscribedEmailById($id);

            if(!$email){
                return new JsonResponse(['status' => 'err', 'data' => 'Invalid email']);
            }

            $this->get('model.notify')->unsubscribe(
                $email,
                $request->request->get('reason_status'),
                $request->request->get('reason_text')
            );

            $response = ['status' => 'ok'];
        } catch (\Exception $e) {
            $this->get('logger')->error($e->getMessage());
            $response = ['status' => 'err', 'data' => 'Internal error'];
        }

        return new JsonResponse($response);
    }
}
