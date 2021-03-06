<?php

/**
 * freelansim.ru task parser
 *
 * @author ndawn (Max Burmistrov) <burmistrovm@live.ru>
*/

namespace app\components\common;

require __DIR__.'/../simple_html_dom.php';

/**
 * Class Parser
 * @package app\components\common
 *
 * Performs the parsing
 */
class Parser {
    /**
     * @var string CSS selector for task container element
     */
    const CONTAINER = '.content-list_tasks';

    /**
     * @var string CSS selector for task price info element
     */
    const PRICE_SECTION = '.task__price';

    /**
     * @var string CSS selector for price value element
     */
    const PRICE = '.count';

    /**
     * @var string CSS selector for price suffix element
     */
    const PRICE_SUFFIX = '.suffix';

    /**
     * @var string CSS selector for negotiated price element
     */
    const PRICE_NEGOTIATED = '.negotiated_price';

    /**
     * @var string CSS selector for task title element (which also contains task id)
     */
    const TITLE = '.task__title a';

    /**
     * @var string CSS selector for task's tags container element
     */
    const TAGS = '.task__tags';

    /**
     * @var string CSS selector for pagination a's
     */
    const PAGINATION_LINK = '.pagination a';

    /**
     * @var string|null URL to parse
     */
    private $url = null;

    /**
     * @var string|null Tags glued to be an URL param
     */
    private $tags = null;


    /**
     * Parser constructor
     * Initialises given params
     *
     * @param mixed[] $params associative array of parameters
     *
     * @throws \InvalidArgumentException if there is no url in $params
     */
    function __construct($params) {
        if (isset($params['url'])) {
            $this->url = $params['url'];
        } else {
            throw new \InvalidArgumentException('No URL specified');
        }

        if (isset($params['tags'])) {
            $tags = array_map(function ($tag) {
                return '[' . $tag . ']';
            }, $params['tags']);
            $this->tags = join('', $tags);
        }
    }


    /**
     * Searches for the task container element by the CSS selector in given page object
     *
     * @uses Parser::CONTAINER selector to search
     * @see Parser::CONTAINER
     *
     * @param \simple_html_dom $page Parsed page object
     * @return \simple_html_dom_node|null Task container element
     */
    private function getContainerNode($page) {
        return $page->find($this::CONTAINER, 0);
    }


    /**
     * Searches for the task elements by the CSS selector in given container node
     *
     * @param \simple_html_dom_node $container Task container element
     * @return \simple_html_dom_node[] array of task elements
     */
    private function getTaskNodes($container) {
        return $container->find('li');
    }


    /**
     * Extracts tags from given container node and pushes them to an array
     *
     * @param \simple_html_dom_node $tagsNode Tags container element
     * @return string[] Array of tags
     */
    private function getTags($tagsNode) {
        $tags = [];
        foreach ($tagsNode->find('li') as $tagNode) {
            array_push($tags, $tagNode->find('a', 0)->text());
        }
        return $tags;
    }


    /**
     * Checks whether given price info node has price value in it or not;
     * if true, returns associative array of price value and suffix;
     * else price value is set to negotiated and the suffix set to empty string
     *
     * @uses Parser::PRICE_SECTION selector to search
     * @see Parser::PRICE_SECTION
     *
     * @uses Parser::PRICE selector to search
     * @see Parser::PRICE
     *
     * @uses Parser::PRICE_SUFFIX selector to search
     * @see Parser::PRICE_SUFFIX
     *
     * @uses Parser::PRICE_NEGOTIATED selector to search
     * @see Parser::PRICE_NEGOTIATED
     *
     * @param \simple_html_dom_node $priceNode Price info container element
     * @return string[] Associative array of price value and price suffix
     */
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


    /**
     * Extracts task data from given task node to an associative array;
     * if task title equals to null then returns null
     *
     * @uses Parser::TITLE selector to search
     * @see Parser::TITLE
     *
     * @uses Parser::TAGS selector to search
     * @see Parser::TAGS
     *
     * @uses Parser::PRICE_SECTION selector to search
     * @see Parser::PRICE_SECTION
     *
     * @uses Parser::getTags() to extract tag list
     * @see Parser::getTags()
     *
     * @uses Parser::getPrice() to extract price info
     * @see Parser::getPrice()
     *
     * @param \simple_html_dom_node $taskNode Task element
     * @return mixed[]|null Associative array of task parameters
     */
    private function getTask($taskNode) {
        $title = $taskNode->find($this::TITLE, 0);

        if (!is_null($title)) {
            $taskLinkExploded = explode('/', $title->attr['href']);

            return [
                'id' => intval($taskLinkExploded[count($taskLinkExploded) - 1]),
                'link' => $title->attr['href'],
                'title' => trim($taskNode->find($this::TITLE, 0)->text()),
                'tags' => $this->getTags($taskNode->find($this::TAGS, 0)),
                'price' => $this->getPrice($taskNode->find($this::PRICE_SECTION, 0))
            ];
        } else {
            return null;
        }
    }


    /**
     * Extracts task data for every task node found on the page and pushes it in an array
     *
     * @uses Parser::getTaskNodes() to get task elements from given container element
     * @see Parser::getTaskNodes()
     *
     * @uses Parser::getTask() to extract task data from task node
     * @see Parser::getTask()
     *
     * @param \simple_html_dom_node $containerNode Task container element
     * @return array[] Array of task arrays
     */
    private function getTasks($containerNode) {
        $tasks = [];

        foreach ($this->getTaskNodes($containerNode) as $taskNode) {
            $task = $this->getTask($taskNode);
            if (!is_null($task)) {
                array_push($tasks, $task);
            }
        }

        return $tasks;
    }


    /**
     * Requests an URL and creates a page DOM element out of response body
     *
     * @uses Parser::url to make request
     * @see Parser::url
     *
     * @uses Parser::tags to add in URL string if not null
     * @see Parser::tags
     *
     * @param integer|null $pageNumber Number of page to make tree from
     * @return \simple_html_dom|bool page DOM element
     */
    private function getPageTree($pageNumber=null) {
        $htmlRaw = file_get_contents(
            $this->url .
            (!is_null($pageNumber) ? '?page=' . $pageNumber : '') .
            (!is_null($this->tags) ? (!is_null($pageNumber) ? '&' : '?') . 'q=' . $this->tags : '')
        );
        return str_get_html($htmlRaw);
    }


    /**
     * Parses the task list for given page
     *
     * @uses Parser::getPageTree() to get page DOM element
     * @see Parser::getPageTree()
     *
     * @uses Parser::getTasks() to extract task data
     * @see Parser::getTasks()
     *
     * @uses Parser::getContainerNode() to get task container element
     * @see Parser::getContainerNode()
     *
     * @param integer|null $pageNumber number of page to parse
     * @return array[] Parsed data
     */
    public function parse($pageNumber=null) {
        $page = $this->getPageTree($pageNumber);

        $taskContainer = $this->getContainerNode($page);

        return !is_null($taskContainer) ? $this->getTasks($taskContainer) : [];
    }
}
