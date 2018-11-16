<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;

class EntryDraftConnection extends Connection {

    /**
     * @return EntryDraftEdge[]
     */
    function getEdges() {
        return array_map(function ($draft) {
            return new EntryDraftEdge($draft);
        }, $this->elements);
    }

}