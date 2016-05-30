<?php

namespace AppBundle\Queue\Consumer;

use AppBundle\Queue\Message\EmailMessage;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class EmailConsumer implements ConsumerInterface
{
    private $mailer;
    private $email;

    const SLEEP_TIME = 3;

    public function __construct(\Swift_Mailer $mailer, $email)
    {
        echo date(\DateTime::ATOM) . ' connection to socket success' . PHP_EOL;

        $this->mailer = $mailer;
        $this->email = $email;
    }

    public function execute(AMQPMessage $msg)
    {
        $data = unserialize($msg->body);

        try {
            echo date(\DateTime::ATOM) . ' ' . $data->email . PHP_EOL;
            $this->run($data);

        } catch (\Exception $e) {
            echo date(\DateTime::ATOM) . ' Error: ' . $data->email . ' message: ' . $e->getMessage() . PHP_EOL;
            sleep(self::SLEEP_TIME);
            return false;
        }

        return true;
    }

    private function run(EmailMessage $msg)
    {
        $mailer = $this->mailer;

        $message = $mailer->createMessage()
            ->setSubject($msg->subject)
            ->setTo($msg->email)
            ->setFrom($this->email)
            ->setBody($msg->body, 'text/html');

        $mailer->send($message);
    }
}