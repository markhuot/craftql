<?php

namespace markhuot\CraftQL\Types;

use Craft;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Request;
use markhuot\CraftQL\Builders\Schema;

class EntryType extends Schema {

    function boot() {
            $this->addRawIntField('id')->nonNull();
            $this->addRawStringField('name')->nonNull();
            $this->addRawStringField('handle')->nonNull();
            $this->addRawField('fields')
                ->nonNull()
                ->lists()
                ->type(Field::class)->resolve(function ($root, $args) {
                    return Craft::$app->fields->getLayoutById($root->fieldLayoutId)->getFields();
                });
    }

}