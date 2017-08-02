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

    static function args() {
        return [
            'after' => Type::string(),
            'ancestorOf' => Type::int(),
            'ancestorDist' => Type::int(),
            'archived' => Type::boolean(),
            'authorGroup' => Type::string(),
            'authorGroupId' => Type::int(),
            'authorId' => Type::int(),
            'before' => Type::string(),
            'level' => Type::int(),
            'localeEnabled' => Type::boolean(),
            'descendantOf' => Type::int(),
            'descendantDist' => Type::int(),
            'fixedOrder' => Type::boolean(),
            'id' => Type::int(),
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
            'section' => Type::string(),
            'siblingOf' => Type::int(),
            'slug' => Type::string(),
            'status' => Type::string(),
            'title' => Type::string(),
            'type' => Type::string(),
            'uri' => Type::string(),
        ];
    }

    static function baseFields() {
        if (!empty(static::$baseFields)) {
            return static::$baseFields;
        }

        $sectionType = new ObjectType([
            'name' => 'Section',
            'fields' => [
                'id' => ['type' => Type::nonNull(Type::int())],
                'structureId' => ['type' => Type::nonNull(Type::int())],
                'name' => ['type' => Type::nonNull(Type::string())],
                'handle' => ['type' => Type::nonNull(Type::string())],
                'type' => ['type' => Type::nonNull(Type::string())],
                'template' => ['type' => Type::string()],
                'maxLevels' => ['type' => Type::int()],
                'hasUrls' => ['type' => Type::boolean()],
                'enableVersioning' => ['type' => Type::boolean()],
            ],
        ]);

        $entryType = new ObjectType([
            'name' => 'EntryType',
            'fields' => [
                'id' => ['type' => Type::nonNull(Type::int())],
                'name' => ['type' => Type::nonNull(Type::string())],
                'handle' => ['type' => Type::nonNull(Type::string())],
            ],
        ]);

        $userStatusEnum =  new EnumType([
            'name' => 'UserStatusEnum',
            'values' => [
                'active',
                'locked',
                'suspended',
                'pending',
                'archived',
            ],
        ]);

        $userFields = [
            'id' => ['type' => Type::nonNull(Type::int())],
            'name' => ['type' => Type::nonNull(Type::string())],
            'fullName' => ['type' => Type::string()],
            'friendlyName' => ['type' => Type::nonNull(Type::string())],
            'firstName' => ['type' => Type::string()],
            'lastName' => ['type' => Type::string()],
            'username' => ['type' => Type::nonNull(Type::string())],
            'email' => ['type' => Type::nonNull(Type::string())],
            'admin' => ['type' => Type::nonNull(Type::boolean())],
            'isCurrent' => ['type' => Type::nonNull(Type::boolean())],
            'preferredLocale' => ['type' => Type::string()],
            'status' => ['type' => Type::nonNull($userStatusEnum)],
        ];
        $fieldService = \Yii::$container->get(\markhuot\CraftQL\Services\FieldService::class);
        $userFields = array_merge($userFields, $fieldService->getDateFieldDefinition('dateCreated'));
        $userFields = array_merge($userFields, $fieldService->getDateFieldDefinition('dateUpdated'));
        $userFields = array_merge($userFields, $fieldService->getDateFieldDefinition('lastLoginDate'));
        // $userFields = array_merge($userFields, $fieldService->getFields(7));

        $userType = new ObjectType([
            'name' => 'User',
            'fields' => $userFields,
        ]);

        $fields = [];
        $fields['elementType'] = ['type' => Type::nonNull(Type::string()), 'resolve' => function ($root, $args) {
            return 'Entry';
        }];
        $fields['id'] = ['type' => Type::nonNull(Type::int())];
        $fields['authorId'] = ['type' => Type::nonNull(Type::int())];
        $fields['author'] = ['type' => Type::nonNull($userType)];
        $fields['title'] = ['type' => Type::nonNull(Type::string())];
        $fields['slug'] = ['type' => Type::nonNull(Type::string())];
        $fields = array_merge($fields, $fieldService->getDateFieldDefinition('dateCreated'));
        $fields = array_merge($fields, $fieldService->getDateFieldDefinition('dateUpdated'));
        $fields = array_merge($fields, $fieldService->getDateFieldDefinition('expiryDate'));
        $fields['enabled'] = ['type' => Type::nonNull(Type::boolean())];
        $fields['status'] = ['type' => Type::nonNull(Type::string())];
        $fields['uri'] = ['type' => Type::string()];
        $fields['url'] = ['type' => Type::string()];
        $fields['section'] = ['type' => $sectionType, 'resolve' => function ($root, $args) {
            return $root->section;
        }];
        $fields['type'] = ['type' => $entryType, 'resolve' => function ($root, $args) {
            return $root->type;
        }];

        return static::$baseFields = $fields;
    }

    static function interface() {
        return static::$interface = static::$interface ?: new InterfaceType([
            'name' => 'EntryInterface',
            'description' => 'An entry in Craft',
            'fields' => static::baseFields(),
            'resolveType' => function ($entry) {
                return \markhuot\CraftQL\Types\EntryType::getName($entry->type);
            }
        ]);
    }

}