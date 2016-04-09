<?php

namespace AppBundle\Service;

use Symfony\Bundle\TwigBundle\TwigEngine;

class Mail
{
    private $box;
    private $mailer;
    private $template;

    /**
     * Mail constructor.
     * @param \Swift_Mailer $mailer
     * @param TwigEngine $template
     */
    public function __construct(\Swift_Mailer $mailer, TwigEngine $template)
    {
        $this->mailer = $mailer;
        $this->template = $template;
        $this->box = [];
    }

    /**
     * @param $email
     * @param $community_id
     * @param $needle
     * @param array $comments
     */
    public function addToBox($email, $community_id, $needle, array $comments)
    {
        if (!array_key_exists($email, $this->box)) {
            $this->box[$email] = [];
        }

        if (!array_key_exists($community_id, $this->box[$email])) {
            $this->box[$email][$community_id] = [];
        }

        if (!array_key_exists($needle, $this->box[$email][$community_id])) {
            $this->box[$email][$community_id][$needle] = [];
        }

        foreach ($comments as $c) {
            $this->box[$email][$community_id][$needle][] = $c;//todo
        }
    }

    /**
     * @throws \Twig_Error
     */
    public function send()
    {
        foreach ($this->box as $email => $letter) {
            $body = $this->template->render('AppBundle:Email:notification.html.twig', ['letter' => $letter]);

            $message = \Swift_Message::newInstance()
                ->setSubject('Notificaton')
                ->setFrom('notify@example.com', 'text/html')
                ->setTo($email)
                ->setBody($body);

            $this->mailer->send($message);
        }
    }
}