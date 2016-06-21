<?php

require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Yaml\Parser;
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\Exception\TelegramException;

try {
    $parser = new Parser();
    $parse = $parser->parse(file_get_contents(__DIR__ . '/../app/config/parameters.yml'));
    $parameters = $parse['parameters'];

    $telegram = new Telegram($parameters['bot.api_key'], $parameters['bot.name']);

    $result = $telegram->setWebHook($parameters['bot.hook_url']);
    if ($result->isOk()) {
        file_put_contents('bot.log', $result->getDescription(), FILE_APPEND);
    }
} catch (TelegramException $e) {
    file_put_contents('bot.log', $e->getMessage(), FILE_APPEND);
}