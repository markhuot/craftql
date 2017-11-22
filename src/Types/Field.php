<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;

class Field extends ObjectType {

    static $object;

    static function make($request) {
        if (!empty(static::$object)) {
            return static::$object;
        }

        return static::$object = new ObjectType([
            'name' => 'Field',
            'description' => 'An custom field in Craft',

            // this has to be a callback because the `user` field references a User type
            // that could have an Entries custom field. This is a problem because we have
            // a circullar reference. Our EntryInterface defines a User which defines an
            // Entries field which relies on the EntryInterface. The callback here ensures
            // that the nested Entries field gets a resolved interface.
            'fields' => function () use ($request) {
                return [
                    'name' => Type::string(),
                    'handle' => Type::string(),
                    'fieldType' => ['type' => Type::string(), 'resolve' => function ($root, $args) {
                        return get_class($root);
                    }],
                    'settings' => ['type' => Type::string(), 'resolve' => function ($root, $args) {
                        return json_encode($root['settings']);
                    }],
                ];
            }
        ]);
    }

}