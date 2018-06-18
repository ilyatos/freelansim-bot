<?php
/**
 * Created by PhpStorm.
 * User: IlyaGoryachev
 * Date: 18.06.2018
 * Time: 16:13
 */

namespace app\components\bot;


class SimpleMessageSender
{
    public function __construct(\Telegram $telegram)
    {
        $this->telegram = $telegram;
    }

    public function sendMessage($chatId, $message)
    {
        $content = [
            'chat_id' => $chatId,
            'text' => $message
        ];

        $this->telegram->sendMessage($content);
    }
}