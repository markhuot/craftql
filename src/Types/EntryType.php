<?php

namespace markhuot\CraftQL\Types;

use Craft;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Request;

class EntryType extends ObjectType {

    protected function fields(Request $request) {
        return [
            'id' => ['type' => Type::nonNull(Type::int())],
            'name' => ['type' => Type::nonNull(Type::string())],
            'handle' => ['type' => Type::nonNull(Type::string())],
            'fields' => ['type' => Type::listOf(Field::make($request)), 'resolve' => function ($root, $args) {
                return Craft::$app->fields->getLayoutById($root->fieldLayoutId)->getFields();
            }],
        ];
    }

}