<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;

class TagGroup extends ObjectType {

    static $type;

    function __construct($group, $request) {
        $fieldService = \Yii::$container->get(\markhuot\CraftQL\Services\FieldService::class);
        $baseFields = [];
        $baseFields['id'] = ['type' => Type::int()];
        $baseFields['title'] = ['type' => Type::string()];
        $fields = array_merge($baseFields, $fieldService->getFields($group->fieldLayoutId, $request));

        parent::__construct([
            'name' => ucfirst($group->handle).'Tags',
            'fields' => $fields,
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
