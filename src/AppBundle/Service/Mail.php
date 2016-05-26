<?php

namespace AppBundle\Service;

use AppBundle\C;
use AppBundle\Entity\City;
use AppBundle\Object\Email;
use AppBundle\Object\HandledComment;
use AppBundle\Object\Message;
use AppBundle\Storage\CommentStorage;
use AppBundle\Storage\HandledCommentStorage;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\TwigBundle\TwigEngine;

class Mail
{
    private $template;
    private $repo_subway;
    private $repo_bind;
    private $storage_comment;
    private $storage_handled_comments;

    public function __construct(
        EntityManager $em,
        TwigEngine $template,
        CommentStorage $storage_comment,
        HandledCommentStorage $storage_handled_comments
    )
    {
        $this->repo_subway = $em->getRepository(C::REPO_SUBWAY);
        $this->repo_bind = $em->getRepository(C::REPO_BIND);
        $this->template = $template;
        $this->storage_handled_comments = $storage_handled_comments;
        $this->storage_comment = $storage_comment;
        $this->subways = $em->getRepository(C::REPO_SUBWAY)->findAllNamesIndexById();
    }

    public function handle(City $city)
    {
        $emails = [];
        foreach($this->repo_bind->findBy(['city' => $city]) as $bind) {
            $email_id = $bind->getEmail()->getId();

            if(!array_key_exists($email_id, $emails)){
                $emails[$email_id] = new Email();
            }

            $email = &$emails[$email_id];
            $email->setEmail($bind->getEmail()->getEmail());
            $email->addSubwayId($bind->getSubway()->getId());
        }

        foreach($emails as $email) {
            $email_subway_ids = $email->getSubwayIds();
            $message = new Message();
            $message->setEmail($email->getEmail());

            foreach($this->storage_handled_comments->getAll() as $handled_comment) {
                $message_subway_ids = array_intersect($email_subway_ids, $handled_comment->getSubwayIds());
                if($message_subway_ids) {
                    $comment = new HandledComment($handled_comment->getId());
                    $comment->setSubwayIds($message_subway_ids);
                    $message->addComment($comment);
                }
            }

            if(!$message->isEmptyComments()) {
                $this->send($message);
            }
        }
    }

    public function send(Message $message)
    {
        $body = $this->template->render('AppBundle:Email:notification.html.twig',
            [
                'handled_comments' => $message->getComments(),
                'comments' => $this->storage_comment->getAll(),
                'subways' => $this->subways
            ]
        );

        file_put_contents($message->getEmail() . date('_H:i:s') . '.html', $body);

//        $swift_message = \Swift_Message::newInstance()
//            ->setSubject('VK Notify')
//            ->setFrom('notify@vn.suntwirl.ru')
//            ->setTo($message->getEmail())
//            ->setBody($body, 'text/html');
//
//        $this->mailer->send($swift_message);
    }
}