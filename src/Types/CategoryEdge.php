<?php

namespace markhuot\CraftQL\Types;

// use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Request;
use markhuot\CraftQL\Builders\Schema;
use markhuot\CraftQL\Traits\HasRelatedEntriesField;

class CategoryEdge extends Schema {

    use HasRelatedEntriesField;

    function boot() {
        $this->addRawStringField('cursor');

        $this->addRawField('node')
            ->type(CategoryInterface::class)
            ->resolve(function ($root) {
                return $root['node'];
            });
    }

}