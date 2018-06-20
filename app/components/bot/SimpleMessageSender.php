<?php

namespace app\components\bot;

/**
 * Class SimpleMessageSender
 * @package app\components\bot
 */
class SimpleMessageSender
{
    /**
     * SimpleMessageSender constructor.
     *
     * @param \Telegram $telegram
     */
    public function __construct(\Telegram $telegram)
    {
        $this->telegram = $telegram;
    }

    /**
     * Send simple message function.
     *
     * @param $chatId integer
     * @param $message string
     */
    public function sendMessage($chatId, $message)
    {
        $content = [
            'chat_id' => $chatId,
            'text' => $message
        ];

        $this->telegram->sendMessage($content);
    }
}