<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;

class EntryDraftConnection extends EntryConnection {

    static $type;

    static function listsType($request) {
        return EntryDraftEdge::type($request);
    }

}