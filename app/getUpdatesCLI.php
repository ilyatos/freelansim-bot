<?php

require '../vendor/autoload.php';

use \Longman\TelegramBot\Telegram;

$bot_api_key  = '565720307:AAGIrB3yGu8IYd2-3nGH4J2wLBraLgzWwFs';
$bot_username = 'freelansim_gl_bot';
$mysql_credentials = [
    'host'     => 'localhost',
    'user'     => 'root',
    'password' => 'root',
    'database' => 'freelansim-bot',
];

try {
    while (true) {
        // Create Telegram API object
        $telegram = new Telegram($bot_api_key, $bot_username);

        // Enable MySQL
        //$telegram->enableMySql($mysql_credentials);
        $telegram->useGetUpdatesWithoutDatabase();

        // Handle telegram getUpdates request
        $response = $telegram->handleGetUpdates();
        sleep(5);
        echo $response;
    }
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    // log telegram errors
     echo $e->getMessage();
}
