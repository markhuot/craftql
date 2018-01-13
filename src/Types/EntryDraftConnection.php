<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;

class EntryDraftConnection extends EntryConnection {

    function boot() {
        parent::boot();

        $this->getField('edges')
            ->type(EntryDraftEdge::class);
    }

}