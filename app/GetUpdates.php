<?php

namespace app;

use Longman\TelegramBot\Telegram;

class GetUpdates
{
    private $BOT_API_KEY  = '565720307:AAGIrB3yGu8IYd2-3nGH4J2wLBraLgzWwFs';
    private $BOT_USERNAME = 'freelansim_gl_bot';

    public function action()
    {
        try {
            // Create Telegram API object
            $telegram = new Telegram($this->BOT_API_KEY, $this->BOT_USERNAME);

            $telegram->useGetUpdatesWithoutDatabase();
            $response = $telegram->handleGetUpdates();

            // Make data proper to use
            $results = json_encode($response->getResult());
            $results = json_decode($results, true);

            return $results;

        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}