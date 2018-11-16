<?php

namespace markhuot\CraftQL\Types;

use craft\base\Element;

abstract class Connection {

    /**
     * Our internal list of edges
     *
     * @var \craft\base\Element[]
     */
    protected $elements;

    /**
     * @var PageInfo
     */
    protected $pageInfo;

    /**
     * Connection constructor.
     * @param $pageInfo PageInfo
     * @param $elements \craft\base\Element[]
     */
    function __construct($pageInfo, $elements) {
        $this->pageInfo = $pageInfo;
        $this->elements = $elements;
    }

    /**
     * @return int
     */
    function getTotalCount() {
        return $this->pageInfo->total;
    }

    /**
     * Return the edges. Make sure you typehint the return so
     * GraphQL can pick up the type
     */
    abstract function getEdges();

}