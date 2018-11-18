<?php

namespace markhuot\CraftQL\Types;

class EntryConnection extends Connection {

    /**
     * @return EntryEdge[]
     */
    function getEdges() {
        return array_map(function ($entry) {
            return new EntryEdge($entry);
        }, $this->elements);
    }

    /**
     * @return EntryInterface[]
     */
    function getEntries() {
        return $this->elements;
    }

}