<?php

namespace app\components\bot;

use app\components\common\Parser;

class ScheduleParser
{

    /**
     * ScheduleParser constructor.
     */
    public function __construct()
    {
        $this->parser = new Parser(['url' => 'https://freelansim.ru/tasks']);
    }


    /**
     * Get parsed results.
     *
     * @return array
     */
    public function getResults()
    {
        return $this->parser->parse();
    }
}