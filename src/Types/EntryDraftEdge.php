<?php

namespace markhuot\CraftQL\Types;

// use GraphQL\Type\Definition\ObjectType;
use markhuot\CraftQL\Builders\Schema;

class EntryDraftEdge extends Schema {

    function boot() {
        $this->addStringField('cursor');

        $this->addField('node')
            ->type(EntryInterface::class)
            ->resolve(function ($root, $args) {
                return $root['node'];
            });

        $this->addField('draftInfo')
            ->type(EntryDraftInfo::class)
            ->resolve(function ($root, $args) {
                return $root['node'];
            });
    }

}
