<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Builders\Schema;
use markhuot\CraftQL\GraphQLFields\Query\Users as UsersField;
use markhuot\CraftQL\GraphQLFields\General\Date as DateField;

class User extends ObjectType {

    static $type;
    static $baseFields;

    static function baseFields($request) {
        if (!empty(static::$baseFields)) {
            return static::$baseFields;
        }

        $schema = new Schema($request);
        $schema->addRawIntField('id')->nonNull();
        $schema->addRawStringField('name')->nonNull();
        $schema->addRawStringField('fullName');
        $schema->addRawStringField('friendlyName')->nonNull();
        $schema->addRawStringField('firstName');
        $schema->addRawStringField('lastName');
        $schema->addRawStringField('username')->nonNull();
        $schema->addRawStringField('email')->nonNull();
        $schema->addRawBooleanField('admin')->nonNull();
        $schema->addRawBooleanField('isCurrent')->nonNull();
        $schema->addRawStringField('preferredLocale');
        $schema->addRawField('status')->type(UsersField::statusEnum())->nonNull();
        $schema->addRawDateField('dateCreated')->nonNull();
        $schema->addRawDateField('dateUpdated')->nonNull();
        $schema->addRawDateField('lastLoginDate')->nonNull();

        return static::$baseFields = $schema->config();
    }

    static function type($request) {
        if (!empty(static::$type)) {
            return static::$type;
        }

        $fieldService = \Yii::$container->get('fieldService');
        $userFieldLayout = \Craft::$app->fields->getLayoutByType(\craft\elements\User::class);

        $userFields = static::baseFields($request);
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