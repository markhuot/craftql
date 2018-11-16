<?php

namespace markhuot\CraftQL\Types;

class TagConnection extends Connection {

    /**
     * @return TagEdge[]
     */
    function getEdges() {
        return array_map(function ($category) {
            return new TagEdge($category);
        }, $this->elements);
    }

    /**
     * @return TagInterface[]
     */
    function getTags() {
        return $this->elements;
    }

}