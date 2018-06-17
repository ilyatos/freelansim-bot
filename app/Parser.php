<?php

require_once '../components/simple_html_dom.php';


class Parser {
    const CONTAINER = '.content-list_tasks';
    const PRICE_SECTION = '.task__price';
    const PRICE = '.count';
    const PRICE_SUFFIX = '.suffix';
    const PRICE_NEGOTIATED = '.negotiated_price';
    const TITLE = '.task__title a';
    const TAGS = '.task__tags';
    const PAGINATION_LINK = '.pagination a';

    private $url = null;
    private $tags = null;


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
        return $page->find($this::CONTAINER, 0);
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
        $hasPrice = is_null($priceNode->find($this::PRICE_NEGOTIATED, 0));

        if ($hasPrice) {
            return [
                'price' => $priceNode->find($this::PRICE, 0)->text(),
                'suffix' => $priceNode->find($this::PRICE_SUFFIX, 0)->text()
            ];
        } else {
            return [
                'price' => $priceNode->find($this::PRICE_NEGOTIATED, 0)->text(),
                'suffix' => ''
            ];
        }
    }


    private function getTask($taskNode) {
        if (!is_null($taskNode->find($this::TITLE, 0))) {
            return [
                'title' => trim($taskNode->find($this::TITLE, 0)->text()),
                'tags' => $this->getTags($taskNode->find($this::TAGS, 0)),
                'price' => $this->getPrice($taskNode->find($this::PRICE_SECTION, 0))
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
        $paginationNode = $page->find($this::PAGINATION_LINK, -2);

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
