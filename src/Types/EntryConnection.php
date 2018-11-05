<?php

namespace markhuot\CraftQL\Types;

use craft\web\twig\variables\Paginate;

class EntryConnection {

    /**
     * Our internal list of entries
     *
     * @var \craft\elements\Entry[]
     */
    private $entries;

    /**
     * @var PageInfo
     */
    public $pageInfo;

    /**
     * CategoryConnection constructor.
     * @param $pageInfo PageInfo
     * @param $entries \craft\elements\Entry[]
     */
    function __construct($pageInfo, $entries) {
        $this->pageInfo = $pageInfo;
        $this->entries = $entries;
    }

    /**
     * @todo nonnull
     * @return int
     */
    function getTotalCount() {
        return $this->pageInfo->total;
    }

    /**
     * @return EntryEdge[]
     */
    function getEdges() {
        return array_map(function ($entry) {
            return new EntryEdge($entry);
        }, $this->entries);
    }

    /**
     * @return EntryInterface[]
     */
    function getEntries() {
        return $this->entries;
    }

}