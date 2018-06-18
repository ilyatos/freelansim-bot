<?php
/**
 * Created by PhpStorm.
 * User: IlyaGoryachev
 * Date: 18.06.2018
 * Time: 16:03
 */

namespace app\components\bot;


class InlineKeyboard
{
    public function __construct(\Telegram $telegram)
    {
        $this->telegram = $telegram;
    }

    public function sendSubs($chatId, $message)
    {
        $option = [
            [
                $this->telegram->buildInlineKeyBoardButton("Подписаться", null,'subscribe'),
                $this->telegram->buildInlineKeyBoardButton("Отписаться", null,'unsubscribe')
            ]
        ];

        $keyb = $this->telegram->buildInlineKeyBoard($option);

        $content =  [
            'chat_id' => $chatId,
            'reply_markup' => $keyb,
            'text' => $message
        ];

        $this->telegram->sendMessage($content);
    }
}