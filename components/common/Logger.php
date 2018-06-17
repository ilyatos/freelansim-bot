<?php
/**
 * Created by PhpStorm.
 * User: IlyaGoryachev
 * Date: 17.06.2018
 * Time: 22:10
 */

namespace components\common;


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