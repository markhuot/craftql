<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Request;
use markhuot\CraftQL\Types\Tag;
use markhuot\CraftQL\Builders\Schema;
use markhuot\CraftQL\Traits\HasRelatedEntriesField;

class TagEdge extends Schema {

    use HasRelatedEntriesField;

    function boot() {
        $this->addStringField('cursor');

        $this->addField('node')
            ->type(TagInterface::class)
            ->resolve(function ($root) {
                return $root['node'];
            });

        // $this->addGlobalField('relatedTo');
    }

}