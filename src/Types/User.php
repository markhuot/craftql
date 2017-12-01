<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\GraphQLFields\Query\Users as UsersField;

class User extends ObjectType {

    static $type;
    static $baseFields;

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
            'status' => ['type' => Type::nonNull(UsersField::statusEnum())],
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

        $userFields = static::baseFields();
        if (!empty($userFieldLayout->id)) {
            $userFields = array_merge($userFields, $fieldService->getFields($userFieldLayout->id, $request));
        }

        return static::$type = new static([
            'name' => 'User',
            'description' => 'A user',
            'fields' => $userFields,
        ]);
    }

}