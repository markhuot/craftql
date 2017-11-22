<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;

class Entry {

    static $interfaces = [];
    static $baseFields;
    static $relatedToInputObject;

    static function baseInputArgs() {
        return [
            'id' => ['type' => Type::int()],
            'authorId' => ['type' => Type::int()],
            'title' => ['type' => Type::string()],
        ];
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
        $fields['type'] = ['type' => \markhuot\CraftQL\Types\EntryType::make($request)];

        $fields['ancestors'] = ['type' => Type::listOf(\markhuot\CraftQL\Types\Entry::interface($request))];
        $fields['children'] = ['type' => Type::listOf(\markhuot\CraftQL\Types\Entry::interface($request))];
        $fields['descendants'] = ['type' => Type::listOf(\markhuot\CraftQL\Types\Entry::interface($request))];
        $fields['hasDescendants'] = ['type' => Type::nonNull(Type::boolean())];
        $fields['level'] = ['type' => Type::int()];
        $fields['parent'] = ['type' => \markhuot\CraftQL\Types\Entry::interface($request)];
        $fields['siblings'] = ['type' => Type::listOf(\markhuot\CraftQL\Types\Entry::interface($request))];

        return static::$baseFields = $fields;
    }

    static function resolveType($entry) {
        return \markhuot\CraftQL\Types\EntryType::getName($entry->type);
    }

    static function interface($request) {
        $reflect = new \ReflectionClass(static::class);
        $shortName = $reflect->getShortName();

        if (!empty(static::$interfaces[$shortName])) {
            return static::$interfaces[$shortName];
        }

        return static::$interfaces[$shortName] = new InterfaceType([
            'name' => $shortName.'Interface',
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
                return static::resolveType($entry);
            }
        ]);
    }

}