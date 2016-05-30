<?php

namespace AppBundle\Queue\Producer;

use AppBundle\Queue\Message\EmailMessage;

class EmailProducer
{
    private $producer;

    public function __construct($producer)
    {
        $this->producer = $producer;
    }

    /**
     * @param $email
     * @param $subject
     * @param $body
     */
    public function send($email, $subject, $body)
    {
        $message = new EmailMessage();
        $message->email = $email;
        $message->subject = $subject;
        $message->body = $body;


        $this->producer->publish(serialize($message));
    }
}