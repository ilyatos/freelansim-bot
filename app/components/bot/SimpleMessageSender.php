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
    public function __construct(\Telegram $telegram, $chatId)
    {
        $this->telegram = $telegram;
        $this->chatId = $chatId;
    }

    /**
     * Send simple message function.
     *
     * @param $chatId integer
     * @param $message string
     */
    public function sendMessage($message)
    {
        $content = [
            'chat_id' => $this->chatId,
            'text' => $message
        ];

        $this->telegram->sendMessage($content);
    }
}