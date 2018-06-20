<?php
/**
 * Created by PhpStorm.
 * User: IlyaGoryachev
 * Date: 18.06.2018
 * Time: 16:03
 */

namespace app\components\bot;

/**
 * Class InlineKeyboard
 *
 * @package app\components\bot
 */
class InlineKeyboard
{
    /**
     * InlineKeyboard constructor.
     *
     * @param \Telegram $telegram
     */
    public function __construct(\Telegram $telegram)
    {
        $this->telegram = $telegram;
    }

    /**
     * Send inline keyboard with message about subscribing
     *
     * @param $chatId integer
     * @param $message string
     */
    public function sendSubs($chatId, $message)
    {
        $option = [
            [
                $this->telegram->buildInlineKeyBoardButton("Подписаться", null,'subscribe_update'),
                $this->telegram->buildInlineKeyBoardButton("Отписаться", null,'unsubscribe_update')
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