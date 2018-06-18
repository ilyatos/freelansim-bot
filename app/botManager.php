<?php

namespace app;

require __DIR__.'/../vendor/autoload.php';

use app\components\bot\TelegramUpdatesManager;
use app\components\common\Logger;

$updater = new TelegramUpdatesManager();
$telegramDict = require_once 'config/telegramDictionary.php';

while (true) {
    // Получаем данные о входящих сообщениях и объект
    $results = $updater->getUpdates();
    $telegram = $updater->getTelegramObj();

    // Если ничего нет, то ничего и не делаем
    if (!empty($results)) {
        // Проходимся циклом по всем принятым сообщениям
        for ($i = 0; $i < $telegram->UpdateCount(); $i++) {
            $telegram->serveUpdate($i);

            $text = $telegram->Text();
            $chatId = $telegram->ChatID();

            // Выводим пришедшее сообщение в консоль
            Logger::log('Сообщение от chat_id = ' . $chatId . ' – ' . $text);

            $answer = function ($message, $dict) {
                if (array_key_exists($message, $dict)) {
                    return $dict[$message];
                } else {
                    return 'Данной команды не существует';
                }
            };

            $content = [
                'chat_id' => $chatId,
                'text' => $answer($text,$telegramDict)
            ];
            
            $telegram->sendMessage($content);
        }
    }
    sleep(1);
}
