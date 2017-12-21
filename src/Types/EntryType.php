<?php

namespace markhuot\CraftQL\Types;

use Craft;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Request;
use markhuot\CraftQL\Builders\Schema;
use craft\models\EntryType as CraftEntryType;

class EntryType extends Schema {

    function boot() {
            $this->addIntField('id')->nonNull();
            $this->addStringField('name')->nonNull();
            $this->addStringField('handle')->nonNull();
            $this->addField('fields')
                ->nonNull()
                ->lists()
                ->type(Field::class)->resolve(function ($root, $args) {
                    return Craft::$app->fields->getLayoutById($root->fieldLayoutId)->getFields();
                });
    }

}