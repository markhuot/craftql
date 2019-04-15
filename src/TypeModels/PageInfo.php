<?php

namespace markhuot\CraftQL\TypeModels;

class PageInfo {

    protected $offset;
    protected $perPage;
    protected $total;

    public $currentPage;
    public $totalPages;
    public $first;
    public $last;

    function __construct($offset, $perPage, $total) {
        $this->offset = $offset;
        $this->perPage = $perPage;
        $this->total = $total;

        $this->currentPage = floor($this->offset / $this->perPage) + 1;
        $this->totalPages = ceil($this->total / $this->perPage);

        // normally currentPage starts at 1, but if there are 0 pages, then drop it down
        if ($this->totalPages == 0) {
            $this->currentPage = 0;
        }

        $this->first = $this->offset;
        $this->last = min($this->total, $this->offset + $this->perPage);
    }

}
