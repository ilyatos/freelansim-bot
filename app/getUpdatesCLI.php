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
    // Create Telegram API object
    $telegram = new Telegram($bot_api_key, $bot_username);

    while (true) {
        sleep(3);

        // Enable MySQL
        $telegram->useGetUpdatesWithoutDatabase();

        // Handle telegram getUpdates request
        $response = $telegram->handleGetUpdates();

        $results = $response->getResult();

        if (!empty($results)) {
            foreach ($results as $result) {
                var_dump($result);
            }
        }
    }
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    // log telegram errors
     echo $e->getMessage();
}
