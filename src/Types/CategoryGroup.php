<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;

class CategoryGroup extends ObjectType {

    static $type;

    function __construct($group, $token) {
        parent::__construct([
            'name' => ucfirst($group->handle).'Category',
            'interfaces' => [
                \markhuot\CraftQL\Types\Category::interface()
            ],
            'fields' => function () use ($group, $token) {
                $fieldService = \Yii::$container->get(\markhuot\CraftQL\Services\FieldService::class);
                $fields = array_merge(\markhuot\CraftQL\Types\Category::baseFields(), $fieldService->getFields($group->fieldLayoutId, $token));
                return $fields;
            },
            'id' => $group->id,
        ]);
    }

    static function type() {
        if (!empty(static::$type)) {
            return static::$type;
        }

        return static::$type = new ObjectType([
            'name' => 'CategoryGroup',
            'fields' => [
                'id' => ['type' => Type::int()],
                'name' => ['type' => Type::string()],
                'handle' => ['type' => Type::string()],
            ],
        ]);
    }

}