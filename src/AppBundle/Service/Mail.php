<?php

namespace AppBundle\Service;

use AppBundle\C;
use AppBundle\Entity\Bind;
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
    private $service_hash;
    private $mailer;

    public function __construct(
        EntityManager $em,
        TwigEngine $template,
        CommentStorage $storage_comment,
        HandledCommentStorage $storage_handled_comments,
        HashService $service_hash,
        \Swift_Mailer $mailer
    )
    {
        $this->repo_subway = $em->getRepository(C::REPO_SUBWAY);
        $this->repo_bind = $em->getRepository(C::REPO_BIND);
        $this->template = $template;
        $this->storage_handled_comments = $storage_handled_comments;
        $this->storage_comment = $storage_comment;
        $this->service_hash = $service_hash;
        $this->mailer = $mailer;
        $this->subways = $em->getRepository(C::REPO_SUBWAY)->findAllNamesIndexById();
    }

    public function handle(City $city)
    {
        $emails = [];
        foreach ($this->repo_bind->findBy(['city' => $city]) as $bind) {

            if ($bind->getStatus() === Bind::STATUS_DELETED) {
                continue;
            }

            $email_id = $bind->getEmail()->getId();

            if (!array_key_exists($email_id, $emails)) {
                $emails[$email_id] = new Email();
                $emails[$email_id]->setEmail($bind->getEmail());
            }

            $emails[$email_id]->addSubwayIdAndHomeType($bind->getSubway()->getId(), $bind->getHomeType());
        }

        $comment_objects = $this->storage_comment->getAll();
        foreach ($emails as $email) {
            $message = new Message();
            $message->setEmail($email->getEmail()->getEmail());
            $message->setEmailHashId($this->service_hash->encode($email->getEmail()->getId()));

            $email_subway_ids = array_keys($email->getSubwayIdAndHomeType());
            $email_subway_ids_and_home_types = $email->getSubwayIdAndHomeType();

            foreach ($this->storage_handled_comments->getAll() as $handled_comment) {
                $message_subway_ids = array_intersect($email_subway_ids, $handled_comment->getSubwayIds());

                $home_type = null;
                foreach ($message_subway_ids as $subway_id) {
                    if (array_key_exists($subway_id, $email_subway_ids_and_home_types)) {
                        $home_type = $email_subway_ids_and_home_types[$subway_id];
                        break;
                    }
                }

                if (null === $home_type) {
                    continue;
                }

                if ($comment_objects[$handled_comment->getid()]->getType() !== $home_type) {
                    continue;
                }

                if ($message_subway_ids) {
                    $comment = new HandledComment($handled_comment->getId());
                    $comment->setSubwayIds($message_subway_ids);
                    $message->addComment($comment);
                }
            }

            if (!$message->isEmptyComments()) {
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
                'subways' => $this->subways,
                'email_hash_id' => $message->getEmailHashId()
            ]
        );

        $swift_message = \Swift_Message::newInstance()
            ->setSubject('VK Notify')
            ->setFrom('notify@vn.suntwirl.ru')
            ->setTo($message->getEmail())
            ->setBody($body, 'text/html');

        $this->mailer->send($swift_message);
    }
}