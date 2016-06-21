<?php

require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Yaml\Parser;
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\Exception\TelegramException;

try {
    $parser = new Parser();
    $parameters = $parser->parse(file_get_contents(__DIR__ . '/../app/config/parameters.yml'));

    $telegram = new Telegram($parameters['bot.api_key'], $parameters['bot.name']);
    $telegram->handle();
} catch (TelegramException $e) {
    file_put_contents('bot.log', $e->getMessage(), FILE_APPEND);
}
