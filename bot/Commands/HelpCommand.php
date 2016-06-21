<?php
require __DIR__ . '/../../vendor/autoload.php';

use Longman\TelegramBot\Commands\Command;
use Longman\TelegramBot\Request;

class HelpCommand extends Command
{
    protected $name = 'help';

    public function execute()
    {
        $update = $this->getUpdate();
        $message = $this->getMessage();

        $chat_id = $message->getChat()->getId();
        $text = $message->getText(true);
        $text = is_numeric($text) ? (int)$text : false;

        $data = array();
        $data['chat_id'] = $chat_id;
        $data['text'] = 'Тестовое сообщение';

        $result = Request::sendMessage($data);
        return $result;
    }
}