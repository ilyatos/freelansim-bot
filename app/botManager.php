<?php

namespace app;

require __DIR__.'/../vendor/autoload.php';

use app\components\bot\InlineKeyboard;
use app\components\bot\ScheduleParser;
use app\components\bot\SimpleMessageSender;
use app\components\bot\TelegramUpdatesManager;
use app\components\common\DbConnection;
use app\components\common\Logger;

$updater = new TelegramUpdatesManager();
$telegramDict = require_once 'config/telegramDictionary.php';

$db = new DbConnection();

$scpar = new ScheduleParser();
$parseResults = $scpar->getResults();

$timeOut = 0;

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

            switch ($text) {
                case '/start':
                    $sms = new SimpleMessageSender($telegram);
                    $sms->sendMessage($chatId, $answer($text, $telegramDict));
                    break;
                case '/subs':
                    $subs = new InlineKeyboard($telegram);
                    $subs->sendSubs($chatId, $answer($text, $telegramDict));
                    break;
                case 'subscribe_update':
                    $db->pushToBase($chatId, 1);
                    $sms = new SimpleMessageSender($telegram);
                    $sms->sendMessage($chatId, $answer($text, $telegramDict));
                    break;
                case 'unsubscribe_update':
                    $db->pushToBase($chatId, 0);
                    $sms = new SimpleMessageSender($telegram);
                    $sms->sendMessage($chatId, $answer($text, $telegramDict));
                    break;
                default:
                    $sms = new SimpleMessageSender($telegram);
                    $sms->sendMessage($chatId, $answer($text, $telegramDict));
            }
        }
    }

    if (intdiv($timeOut, 360) != 0) {
        $scparRes = $scpar->getResults();

        if ($parseResults[0]['id'] !=  $scparRes[0]['id']) {
            $oldResult = $parseResults[0]['id'];
            $parseResults = [];

            foreach ($scparRes as $result) {
                if ($result['id'] != $oldResult) {
                    array_push($parseResults, $result);
                } else {
                    break;
                }
            }

            $subsUsers = $db->getUsers();
            $sms = new SimpleMessageSender($telegram);

            foreach ($subsUsers as $user) {
                $message = '';
                foreach ($parseResults as $job) {
                    $message .= $job['title'].'.'.PHP_EOL;
                }
                $sms->sendMessage($user['chat_id'], $message);
            }
        }
        $timeOut += 1;
    } else {
        $timeOut += 1;
    }
    sleep(1);
}
