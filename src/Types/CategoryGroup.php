<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;

class CategoryGroup extends ObjectType {

    function __construct($group, $token) {
        $fieldService = \Yii::$container->get(\markhuot\CraftQL\Services\FieldService::class);
        $fields = array_merge(\markhuot\CraftQL\Types\Category::baseFields(), $fieldService->getFields($group->fieldLayoutId, $token));

        parent::__construct([
            'name' => ucfirst($group->handle).'Category',
            'interfaces' => [
                \markhuot\CraftQL\Types\Category::interface()
            ],
            'fields' => $fields,
        ]);
    }

}