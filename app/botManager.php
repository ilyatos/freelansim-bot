<?php

namespace app;

require __DIR__.'/../vendor/autoload.php';

use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Commands\UserCommands\InlinekeyboardCommand;

use app\GetUpdates;
use components\common\Logger;

$updater = new GetUpdates();
$telegramDict = require_once '../config/telegram_dict.php';

while (true) {
    sleep(1);

    //получает данные о входящих сообщениях
    $results = $updater->action();

    //если ничего нет, то ничего и не делаем
    if (!empty($results)) {

        //проходимся циклом по всем принятым сообщениям
        foreach ($results as $result) {
            $chatId = $result['message']['chat']['id'];
            $message = $result['message']['text'];

            //выводим пришедшее сообщение в консоль
            Logger::log('Сообщение от chat_id = '. $chatId.' – '.$message);

            $answer = function ($message, $dict) {
                if (array_key_exists($message, $dict)) {
                    return $dict[$message];
                } else {
                    return 'Данной команды не существует';
                }
            };

            $data = [
                'chat_id' => $chatId,
                'text' => $answer($message, $telegramDict)
            ];

            Request::sendMessage($data);
        }
    }
}
