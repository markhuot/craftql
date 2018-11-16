<?php

namespace markhuot\CraftQL\Types;

class CategoryConnection extends Connection {

    /**
     * @return CategoryEdge[]
     */
    function getEdges() {
        return array_map(function ($category) {
            return new CategoryEdge($category);
        }, $this->elements);
    }

    /**
     * @return CategoryInterface[]
     */
    function getCategories() {
        return $this->elements;
    }

}