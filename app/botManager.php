<?php

namespace app;

require __DIR__.'/../vendor/autoload.php';

use app\components\bot\InlineKeyboard;
use app\components\bot\ScheduleParser;
use app\components\bot\SimpleMessageSender;
use app\components\bot\TelegramUpdatesManager;
use app\components\common\User;
use app\components\common\Logger;
use app\components\common\UserTag;

$updater = new TelegramUpdatesManager();
$telegramDict = require_once 'config/telegramDictionary.php';

$user = new User();
$userTag = new UserTag();

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


            $sms = new SimpleMessageSender($telegram, $chatId);
            $explText = explode(' ', $text);

            switch ($explText[0]) {
                case '/start':
                    $sms->sendMessage($answer($text, $telegramDict));
                    break;
                case '/subs':
                    $subs = new InlineKeyboard($telegram);
                    $subs->sendSubs($chatId, $answer($text, $telegramDict));
                    break;
                case 'subscribe_update':
                    $user->pushToBase($chatId, 1);
                    $sms->sendMessage($answer($text, $telegramDict));
                    break;
                case 'unsubscribe_update':
                    $user->pushToBase($chatId, 0);
                    $sms->sendMessage($answer($text, $telegramDict));
                    break;
                case '/tags':
                    $utArray = $userTag->getUserTags($chatId);
                    $message = 'Ваши теги:'.PHP_EOL;
                    foreach ($utArray as $ut) {
                        $message .= $ut['tag'].PHP_EOL;
                    }
                    $sms->sendMessage($message);
                    break;
                case '/subs_tag':
                    if (!array_key_exists(1, $explText)) {
                        $sms->sendMessage($answer($explText[0], $telegramDict));
                        break;
                    }
                    $tag = $explText[1];
                    if (!$userTag->pushTagToBase($chatId, $tag)) {
                        $sms->sendMessage('Вы уже подписаны на тег: `'.$tag.'`');
                    } else {
                        $sms->sendMessage('Вы успешно подписались на тег: `'.$tag.'`');
                    }
                    break;
                case '/unsubs_tag':
                    if (!array_key_exists(1, $explText)) {
                        $sms->sendMessage($answer($explText[0], $telegramDict));
                        break;
                    }
                    $tag = $explText[1];
                    if (!$userTag->deleteTagFromBase($chatId, $tag)) {
                        $sms->sendMessage('Вы уже отписаны от тега: `'.$tag.'`');
                    } else {
                        $sms->sendMessage('Вы успешно отписались от тега: `'.$tag.'`');
                    }
                    break;
                default:
                    $sms->sendMessage($answer($text, $telegramDict));
            }
        }
    }
    
    //рассылка
    if (intdiv($timeOut, 1800) != 0) {
        $scparRes = $scpar->getResults();

        //test data
       /* $scparRes[2]['id'] = $scparRes[0]['id'];
        $scparRes[0]['id'] = 32331;
        $scparRes[1]['id'] = 33434;*/

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

            $subsUsers = $user->getUsers();

            foreach ($subsUsers as $user) {
                $sms = new SimpleMessageSender($telegram, $user['chat_id']);
                //рассылка по тегам
                if ($tags = $userTag->getUserTags($user['chat_id'])) {
                    $message = '';
                    foreach ($parseResults as $job) {
                        $intersection = array_intersect($job['tags'], $tags);
                        if (!empty($intersection)) {
                            $message .= $job['title'].'.'.PHP_EOL;
                            $message .= ' Теги: '. implode('-',$intersection).PHP_EOL;
                        }
                    }
                } else {
                    //рассылка без тегов
                    $message = '';
                    foreach ($parseResults as $job) {
                        $message .= $job['title'].'.'.PHP_EOL;
                    }
                }
                $sms->sendMessage($message);
            }
        }
        $timeOut += 1;
    } else {
        $timeOut += 1;
    }
    sleep(1);
}