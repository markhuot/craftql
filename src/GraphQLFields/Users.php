<?php

namespace markhuot\CraftQL\GraphQLFields;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\EnumType;
use markhuot\CraftQL\GraphQLFields\Base as BaseField;
use markhuot\CraftQL\Types\User;

class Users extends BaseField {

    protected $description = 'Users registered in Craft';
    static $statusEnum;

    function getType() {
        return Type::listOf(User::type($this->request));
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

    function getArgs() {
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

    function getResolve($root, $args, $context, $info) {
        $criteria = \craft\elements\User::find();

        foreach ($args as $key => $value) {
            $criteria = $criteria->{$key}($value);
        }

        return $criteria->all();
    }

}