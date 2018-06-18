<?php

namespace app\components\bot;

use Telegram;

class TelegramUpdatesManager
{
    private $BOT_API_KEY  = '565720307:AAGIrB3yGu8IYd2-3nGH4J2wLBraLgzWwFs';

    public function __construct()
    {
        // Создание объекта
        $this->telegram = new Telegram($this->BOT_API_KEY);
    }

    public function getUpdates()
    {
        try {
            // Получение апдейтов
            $response = $this->telegram->getUpdates();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        return $response;
    }

    public function getTelegramObj()
    {
        return $this->telegram;
    }

}