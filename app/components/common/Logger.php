<?php

namespace app\components\common;


class Logger
{
    private const AA = 2;

    public static function log($message)
    {
        $currentTime = date('H:i:s');
        $forLog = '~~~ ['.$currentTime.'] – '.$message.'. ~~~'.PHP_EOL;
        echo $forLog;
    }
}