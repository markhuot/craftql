<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;

class Tag extends ObjectType {

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
        $fields['group'] = ['type' => \markhuot\CraftQL\Types\TagGroup::type()];

        return static::$baseFields = $fields;
    }

    static function args($request) {
        return [
            'fixedOrder' => Type::boolean(),
            'group' => $request->tagGroups()->enum(),
            'groupId' => Type::int(),
            'id' => Type::int(),
            'indexBy' => Type::string(),
            'limit' => Type::int(),
            'locale' => Type::string(),
            'offset' => Type::int(),
            'order' => Type::string(),
            'relatedTo' => Type::listOf(Entry::relatedToInputObject()),
            'search' => Type::string(),
            'slug' => Type::string(),
            'title' => Type::string(),
        ];
    }

    static function interface() {
        if (!static::$interface) {
            $fields = static::baseFields();

            static::$interface = new InterfaceType([
                'name' => 'TagInterface',
                'description' => 'A tag in Craft',
                'fields' => $fields,
                'resolveType' => function ($tag) {
                    return ucfirst($tag->group->handle).'Tags';
                }
            ]);
        }

        return static::$interface;
    }

}