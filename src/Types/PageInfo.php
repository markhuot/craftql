<?php

namespace markhuot\CraftQL\Types;

use craft\web\twig\variables\Paginate;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Builders\Schema;

class PageInfo {

    /**
     * @var int
     */
    public $limit = 100;

    /**
     * @var int
     */
    public $first;

    /**
     * @var int
     */
    public $last;

    /**
     * @var int
     */
    public $total = 0;

    /**
     * @var int
     */
    public $totalPages = 0;

    /**
     * PageInfo constructor.
     * @param Paginate $paginate
     * @param int $limit
     */
    function __construct(Paginate $paginate, $limit=null) {
        foreach (['first', 'last', 'total', 'totalPages'] as $key) {
            $this->{$key} = $paginate->{$key};
        }
        if (is_numeric($limit)) {
            $this->limit = $limit;
        }
    }

    /**
     * @return int
     */
    function getCurrentPage() {
        return floor(($this->first - 1) / $this->limit) +  1;
    }

    /**
     * @return bool
     */
    function getHasPreviousPage() {
        return $this->getCurrentPage() > 1;
    }

    /**
     * @return bool
     */
    function getHasNextPage() {
        return $this->getCurrentPage() < $this->totalPages;
    }

}