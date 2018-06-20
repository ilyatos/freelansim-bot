<?php

namespace app\components\bot;

use Telegram;

/**
 * Class TelegramUpdatesManager
 *
 * @package app\components\bot
 */
class TelegramUpdatesManager
{
    /**
     * Private key to get access to API
     *
     * @var string
     */
    private $BOT_API_KEY  = '565720307:AAGIrB3yGu8IYd2-3nGH4J2wLBraLgzWwFs';

    /**
     * TelegramUpdatesManager constructor.
     */
    public function __construct()
    {
        // Создание объекта
        $this->telegram = new Telegram($this->BOT_API_KEY);
    }

    /**
     * Get all updates since last update.
     *
     * @return array|mixed
     */
    public function getUpdates()
    {
        try {
            $response = $this->telegram->getUpdates();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        return $response;
    }

    /**
     * Get the object of Telegram class
     *
     * @return Telegram
     */
    public function getTelegramObj()
    {
        return $this->telegram;
    }

}