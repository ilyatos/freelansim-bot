<?php

namespace app\components\common;

/**
 * Class Logger
 *
 * @package app\components\common
 */
class Logger
{
    /**
     * Logging in cmd everything you want.
     *
     * @param $message string
     */
    public static function log($message)
    {
        $currentTime = date('H:i:s');
        $forLog = '~~~ ['.$currentTime.'] – '.$message.'. ~~~'.PHP_EOL;
        echo $forLog;
    }
}