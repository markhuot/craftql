<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Request;
use markhuot\CraftQL\Types\Tag;
use markhuot\CraftQL\Builders\Schema;

class TagEdge extends Schema {

    function boot() {
        $this->addRawStringField('cursor');

        $this->addRawField('node')
            ->type(TagInterface::class)
            ->resolve(function ($root) {
                return $root['node'];
            });

        // $this->addGlobalField('relatedTo');
    }

}