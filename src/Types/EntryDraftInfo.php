<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Builders\Schema;

class EntryDraftInfo extends Schema {

    function boot() {
        $this->addRawIntField('draftId');
        $this->addRawStringField('name');
        $this->addRawStringField('notes')
            ->resolve(function ($root, $args) {
                return $root->revisionNotes;
            });
    }

}