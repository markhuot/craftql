<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;

class Category extends ObjectType {

    static $interface;
    static $baseFields;

    static function baseFields() {
        if (!empty(static::$baseFields)) {
            return static::$baseFields;
        }

        $fields = [];
        $fields['id'] = ['type' => Type::nonNull(Type::int())];
        $fields['title'] = ['type' => Type::nonNull(Type::string())];
        $fields['slug'] = ['type' => Type::string()];
        $fields['uri'] = ['type' => Type::string()];
        $fields['group'] = ['type' => \markhuot\CraftQL\Types\CategoryGroup::type()];

        return static::$baseFields = $fields;
    }

    static function interface() {
        if (!static::$interface) {
            $fields = static::baseFields();

            static::$interface = new InterfaceType([
                'name' => 'Category',
                'description' => 'A category in Craft',
                'fields' => $fields,
                'resolveType' => function ($category) {
                    return ucfirst($category->group->handle).'Category';
                }
            ]);
        }

        return static::$interface;
    }

}