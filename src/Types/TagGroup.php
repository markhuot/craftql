<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;

class TagGroup extends ObjectType {

    static $type;

    function __construct($group, $request) {
        $fieldService = \Yii::$container->get('fieldService');
        $baseFields = [];
        $baseFields['id'] = ['type' => Type::nonNull(Type::int())];
        $baseFields['title'] = ['type' => Type::nonNull(Type::string())];
        $baseFields['slug'] = ['type' => Type::string()];
        $baseFields['group'] = ['type' => \markhuot\CraftQL\Types\TagGroup::type()];
        $fields = array_merge($baseFields, $fieldService->getFields($group->fieldLayoutId, $request));

        parent::__construct([
            'name' => ucfirst($group->handle).'Tags',
            'fields' => $fields,
            'id' => $group->id,
            'interfaces' => [
                Tag::interface(),
            ],
        ]);
    }

    static function type() {
        if (!empty(static::$type)) {
            return static::$type;
        }

        return static::$type = new ObjectType([
            'name' => 'TagGroup',
            'fields' => [
                'id' => ['type' => Type::int()],
                'name' => ['type' => Type::string()],
                'handle' => ['type' => Type::string()],
            ],
        ]);
    }

}
