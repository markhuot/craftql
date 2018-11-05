<?php

namespace markhuot\CraftQL\Types;

use craft\web\twig\variables\Paginate;

class CategoryConnection {

    /**
     * Our internal list of categories
     *
     * @var \craft\elements\Category[]
     */
    private $categories;

    /**
     * @var PageInfo
     */
    private $pageInfo;

    /**
     * CategoryConnection constructor.
     * @param $pageInfo PageInfo
     * @param $categories \craft\elements\Category[]
     */
    function __construct($pageInfo, $categories) {
        $this->pageInfo = $pageInfo;
        $this->categories = $categories;
    }

    /**
     * @return int
     */
    function getTotalCount() {
        return $this->pageInfo->total;
    }

    /**
     * @craftql-return CategoryEdge[]
     * @return \craft\elements\Category[]
     */
    function getEdges() {
        return array_map(function ($category) {
            return new CategoryEdge($category);
        }, $this->categories);
    }

    function getCategories() {
        return $this->categories;
    }

}