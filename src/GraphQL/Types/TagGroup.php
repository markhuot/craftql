<?php

namespace markhuot\CraftQL\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;

class TagGroup extends ObjectType {

    static function make($group) {
        $fieldService = \Yii::$container->get(\markhuot\CraftQL\Services\FieldService::class);

        $tagGroupFields = [];
        $tagGroupFields['id'] = ['type' => Type::int()];
        $tagGroupFields['title'] = ['type' => Type::string()];
        $tagGroupFields = array_merge($tagGroupFields, $fieldService->getFields($group->fieldLayoutId));

        return new static([
            'name' => ucfirst($group->handle).'Tags',
            'fields' => $tagGroupFields,
        ]);
    }

}