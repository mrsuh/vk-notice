<?php

namespace AppBundle\Service;

use AppBundle\C;
use AppBundle\Entity\Community;
use AppBundle\Entity\Needle;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\TwigBundle\TwigEngine;

class Mail
{
    private $box;
    private $mailer;
    private $template;
    private $repo_email;

    /**
     * Mail constructor.
     * @param \Swift_Mailer $mailer
     * @param TwigEngine $template
     */
    public function __construct(EntityManager $em, \Swift_Mailer $mailer, TwigEngine $template)
    {
        $this->repo_email = $em->getRepository(C::REPO_EMAIL);
        $this->mailer = $mailer;
        $this->template = $template;
        $this->box = [];
    }

    /**
     * @param Community $community
     * @param Needle $needle
     * @param array $comment
     */
    public function addToMailBox(Community $community, Needle $needle, array $comment)
    {
        $email = $this->repo_email->findByCommunityAndNeedle($community, $needle);

        if (!array_key_exists($email->getEmail(), $this->box)) {
            $this->box[$email->getEmail()] = [];
        }

        if (!array_key_exists($community->getTopicName(), $this->box[$email->getEmail()])) {
            $this->box[$email->getEmail()][$community->getTopicName()] = [];
        }

        if (!array_key_exists($needle->getId(), $this->box[$email->getEmail()][$community->getTopicName()])) {
            $this->box[$email->getEmail()][$community->getTopicName()][$needle->getNeedle()] = [];
        }

        $this->box[$email->getEmail()][$community->getTopicName()][$needle->getNeedle()][] = $comment;
    }

    /**
     * @throws \Twig_Error
     */
    public function send()
    {
        foreach ($this->box as $email => $letter) {
            $body = $this->template->render('AppBundle:Email:notification.html.twig', ['letter' => $letter]);

            $message = \Swift_Message::newInstance()
                ->setSubject('VK Notify')
                ->setFrom('notify@vn.suntwirl.ru', 'text/html')
                ->setTo($email)
                ->setBody($body, 'text/html');

            $this->mailer->send($message);
        }
    }
}