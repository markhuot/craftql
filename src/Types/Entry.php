<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;

class Entry {

    static $interface;
    static $baseFields;

    static function baseInputArgs() {
        return [
            'id' => ['type' => Type::int()],
            'authorId' => ['type' => Type::int()],
            'title' => ['type' => Type::string()],
        ];
    }

    static function args($request) {
        $args = [
            'after' => Type::string(),
            'ancestorOf' => Type::int(),
            'ancestorDist' => Type::int(),
            'archived' => Type::boolean(),
            'authorGroup' => Type::string(),
            'authorGroupId' => Type::int(),
            'authorId' => Type::listOf(Type::int()),
            'before' => Type::string(),
            'level' => Type::int(),
            'localeEnabled' => Type::boolean(),
            'descendantOf' => Type::int(),
            'descendantDist' => Type::int(),
            'fixedOrder' => Type::boolean(),
            'id' => Type::listOf(Type::int()),
            'limit' => Type::int(),
            'locale' => Type::string(),
            'nextSiblingOf' => Type::int(),
            'offset' => Type::int(),
            'order' => Type::string(),
            'positionedAfter' => Type::id(),
            'positionedBefore' => Type::id(),
            'postDate' => Type::string(),
            'prevSiblingOf' => Type::id(),
            'relatedTo' => Type::id(),
            'search' => Type::string(),
            'section' => Type::listOf($request->sections()->enum()),
            'siblingOf' => Type::int(),
            'slug' => Type::string(),
            'status' => Type::string(),
            'title' => Type::string(),
            'type' => Type::listOf($request->entryTypes()->enum()),
            'uri' => Type::string(),
        ];

        return $args;
    }

    static function baseFields($request) {
        if (!empty(static::$baseFields)) {
            return static::$baseFields;
        }

        $fieldService = \Yii::$container->get(\markhuot\CraftQL\Services\FieldService::class);

        $fields = [];
        $fields['elementType'] = ['type' => Type::nonNull(Type::string()), 'resolve' => function ($root, $args) {
            return 'Entry';
        }];
        $fields['id'] = ['type' => Type::nonNull(Type::int())];

        if ($request->token()->can('query:entry.author')) {
            $fields['author'] = ['type' => Type::nonNull(\markhuot\CraftQL\Types\User::type($request))];
        }
        
        $fields['title'] = ['type' => Type::nonNull(Type::string())];
        $fields['slug'] = ['type' => Type::nonNull(Type::string())];
        $fields = array_merge($fields, $fieldService->getDateFieldDefinition('dateCreated'));
        $fields = array_merge($fields, $fieldService->getDateFieldDefinition('dateUpdated'));
        $fields = array_merge($fields, $fieldService->getDateFieldDefinition('expiryDate'));
        $fields['enabled'] = ['type' => Type::nonNull(Type::boolean())];
        $fields['status'] = ['type' => Type::nonNull(Type::string())];
        $fields['uri'] = ['type' => Type::string()];
        $fields['url'] = ['type' => Type::string()];
        $fields['section'] = ['type' => \markhuot\CraftQL\Types\Section::type()];
        $fields['type'] = ['type' => \markhuot\CraftQL\Types\EntryType::type()];

        return static::$baseFields = $fields;
    }

    static function interface($request) {
        if (!empty(static::$interface)) {
            return static::$interface;
        }

        return static::$interface = new InterfaceType([
            'name' => 'EntryInterface',
            'description' => 'An entry in Craft',

            // this has to be a callback because the `user` field references a User type
            // that could have an Entries custom field. This is a problem because we have
            // a circullar reference. Our EntryInterface defines a User which defines an
            // Entries field which relies on the EntryInterface. The callback here ensures
            // that the nested Entries field gets a resolved interface.
            'fields' => function () use ($request) {
                return static::baseFields($request);
            },

            'resolveType' => function ($entry) {
                return \markhuot\CraftQL\Types\EntryType::getName($entry->type);
            }
        ]);
    }

}