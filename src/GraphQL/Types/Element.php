<?php

namespace markhuot\CraftQL\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;

class Element {

    static $interface;
    static $baseFields;

    static function baseFields() {
        if (!empty(static::$baseFields)) {
            return static::$baseFields;
        }

        $fields = [];
        $fields['elementType'] = ['type' => Type::string()];

        return static::$baseFields = $fields;
    }

    static function interface() {
        if (!static::$interface) {
            $fields = static::baseFields();

            static::$interface = new InterfaceType([
                'name' => 'Element',
                'description' => 'A generic element in Craft',
                'fields' => static::baseFields(),
                'resolveType' => function ($element) {
                    switch ($element->elementType) {
                        case 'Category':
                            return ucfirst($element->group->handle);
                            break;
                        case 'Entry':
                            return ucfirst($element->section->handle);
                            break;
                    }
                }
            ]);
        }

        return static::$interface;
    }

}