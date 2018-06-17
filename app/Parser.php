<?php

namespace app\controllers;

require_once 'components/simple_html_dom.php';


class ClassConstants {
    static $CONTAINER = '.content-list_tasks';
    static $PRICE_SECTION = '.task__price';
    static $PRICE = '.count';
    static $PRICE_SUFFIX = '.suffix';
    static $PRICE_NEGOTIATED = '.negotiated_price';
    static $TITLE = '.task__title a';
    static $TAGS = '.task__tags';
    static $PAGINATION_LINK = '.pagination a';
}


class Parser
{
    private $url;
    private $tags;


    function __construct($params=null) {
        if (!is_null($params)) {
            if (isset($params['url'])) {
                $this->url = $params['url'];
            }

            if (isset($params['tags'])) {
                $tags = array_map(function ($tag) {
                    return '[' . $tag . ']';
                }, $params['tags']);
                $this->tags = join('', $tags);
            }
        }
    }

    private function getContainerNode($page) {
        return $page->find(ClassConstants::$CONTAINER, 0);
    }


    private function getTasksNodes($container) {
        return $container->find('li');
    }


    private function getTags($tagsNode) {
        $tags = [];
        foreach ($tagsNode->find('li') as $tagNode) {
            array_push($tags, $tagNode->find('a', 0)->text());
        }
        return $tags;
    }


    private function getPrice($priceNode) {
        $hasPrice = is_null($priceNode->find(ClassConstants::$PRICE_NEGOTIATED, 0));


        if ($hasPrice) {
            return [
                'price' => $priceNode->find(ClassConstants::$PRICE, 0)->text(),
                'suffix' => $priceNode->find(ClassConstants::$PRICE_SUFFIX, 0)->text()
            ];
        } else {
            return [
                'price' => $priceNode->find(ClassConstants::$PRICE_NEGOTIATED, 0)->text(),
                'suffix' => ''
            ];
        }
    }


    private function getTask($taskNode) {
        if (!is_null($taskNode->find(ClassConstants::$TITLE, 0))) {
            return [
                'title' => trim($taskNode->find(ClassConstants::$TITLE, 0)->text()),
                'tags' => $this->getTags($taskNode->find(ClassConstants::$TAGS, 0)),
                'price' => $this->getPrice($taskNode->find(ClassConstants::$PRICE_SECTION, 0))
            ];
        } else {
            return null;
        }
    }


    private function getTasks($containerNode) {
        $tasks = [];

        foreach ($this->getTasksNodes($containerNode) as $taskNode) {
            $task = $this->getTask($taskNode);
            if (!is_null($task)) {
                array_push($tasks, $task);
            }
        }

        return $tasks;
    }


    private function getPageCount($page) {
        $paginationNode = $page->find(ClassConstants::$PAGINATION_LINK, -2);

        if (!is_null($paginationNode)) {
            $pageCountString = $paginationNode->text();
        } else {
                if (!is_null($this->getContainerNode($page))) {
                    $pageCountString = '1';
                } else {
                    $pageCountString = '0';
                }
        }

        return intval($pageCountString);
    }


    private function getPageTree($pageNumber=null) {
        $htmlRaw = file_get_contents(
            $this->url .
            (!is_null($pageNumber) ? '?page=' . $pageNumber : '') .
            (!is_null($this->tags) ? (!is_null($pageNumber) ? '&' : '?') . 'q=' . $this->tags : '')
        );
        return str_get_html($htmlRaw);
    }


    public function parse() {
        $firstPage = $this->getPageTree();

        $pageCount = $this->getPageCount($firstPage);

        $parsed = [
            'count' => $pageCount,
            'pages' => []
        ];

        if ($pageCount > 0) {
            $parsed['pages'][1] = $this->getTasks($this->getContainerNode($firstPage));
        }

        if ($pageCount > 1) {
            for ($pageNumber = 2; $pageNumber <= $pageCount; $pageNumber++) {
                $parsed['pages'][$pageNumber] = $this->getTasks($this->getContainerNode($this->getPageTree($pageNumber)));
            }
        }

        return $parsed;
    }
}

$parser = new Parser(['url' => 'https://freelansim.ru/tasks', 'tags' => ['python']]);

var_dump($parser->parse());
