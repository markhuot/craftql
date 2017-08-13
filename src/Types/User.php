<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;

class User extends ObjectType {

    static $type;
    static $baseFields;
    static $statusEnum;

    static function baseInputArgs() {
        return [
            
        ];
    }

    static function statusEnum() {
        if (!empty(static::$statusEnum)) {
            return static::$statusEnum;
        }

        return static::$statusEnum =  new EnumType([
            'name' => 'UserStatusEnum',
            'values' => [
                'active',
                'locked',
                'suspended',
                'pending',
                'archived',
            ],
        ]);
    }

    static function args() {
        return [
            'admin' => Type::boolean(),
            'email' => Type::string(),
            'firstName' => Type::string(),
            'group' => Type::string(),
            'groupId' => Type::string(),
            'id' => Type::int(),
            'lastLoginDate' => Type::int(),
            'lastName' => Type::string(),
            'limit' => Type::int(),
            'offset' => Type::int(),
            'order' => Type::string(),
            'search' => Type::string(),
            'status' => static::statusEnum(),
            'username' => Type::string(),
        ];
    }

    static function baseFields() {
        if (!empty(static::$baseFields)) {
            return static::$baseFields;
        }

        $fields = [
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
            'status' => ['type' => Type::nonNull(static::statusEnum())],
        ];

        $fieldService = \Yii::$container->get(\markhuot\CraftQL\Services\FieldService::class);

        $fields = array_merge($fields, $fieldService->getDateFieldDefinition('dateCreated'));
        $fields = array_merge($fields, $fieldService->getDateFieldDefinition('dateUpdated'));
        $fields = array_merge($fields, $fieldService->getDateFieldDefinition('lastLoginDate'));

        return static::$baseFields = $fields;
    }

    static function type($request) {
        if (!empty(static::$type)) {
            return static::$type;
        }

        $fieldService = \Yii::$container->get(\markhuot\CraftQL\Services\FieldService::class);
        $userFieldLayout = \Craft::$app->fields->getLayoutByType(\craft\elements\User::class);
        $userFields = array_merge(static::baseFields(), $fieldService->getFields($userFieldLayout->id, $request));

        return static::$type = new static([
            'name' => 'User',
            'description' => 'A user',
            'fields' => $userFields,
        ]);
    }

}