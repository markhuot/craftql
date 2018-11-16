<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\FieldTraits\RelatedEntries;

class EntryConnection extends Connection {

    use RelatedEntries;

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