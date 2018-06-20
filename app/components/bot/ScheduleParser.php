<?php

namespace app\components\bot;

use app\components\common\Parser;

class ScheduleParser
{
    private $results = [];

    public function __construct()
    {
        $this->parser = new Parser(['url' => 'https://freelansim.ru/tasks']);
    }


    /**
     * @return array
     */
    public function getResults()
    {
        $this->results = $this->parser->parse();
        return $this->results;
    }
}