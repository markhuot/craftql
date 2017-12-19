<?php

namespace markhuot\CraftQL\Types;

// use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Request;
use markhuot\CraftQL\Builders\Schema;

class EntryDraftEdge extends Schema {

    function boot() {
        $this->addRawStringField('cursor');

        $this->addRawField('node')
            ->type(EntryInterface::class)
            ->resolve(function ($root, $args) {
                return $root['node'];
            });

        $this->addRawField('draftInfo')
            ->type(EntryDraftInfo::class)
            ->resolve(function ($root, $args) {
                return $root['node'];
            });
    }

}