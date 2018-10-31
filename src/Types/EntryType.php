<?php

namespace markhuot\CraftQL\Types;

use Craft;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Request;
use markhuot\CraftQL\Builders\Schema;
use craft\models\EntryType as CraftEntryType;
use markhuot\CraftQL\Helpers\StringHelper;

class EntryType extends Schema {

    function boot() {
        $this->addIntField('id')->nonNull();
        $this->addStringField('name')->nonNull();
        $this->addStringField('handle')->nonNull();
        $this->addStringField('graphQlTypeName')->nonNull();
        $this->addField('fields')
            ->nonNull()
            ->lists()
            ->type(Field::class);
    }

}