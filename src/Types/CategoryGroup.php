<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;

class CategoryGroup extends ObjectType {

    static $interface;
    static $baseFields;

    static function make($group) {
        $fieldService = \Yii::$container->get(\markhuot\CraftQL\Services\FieldService::class);
        $fields = array_merge(\markhuot\CraftQL\Types\Category::baseFields(), $fieldService->getFields($group->fieldLayoutId));

        return new static([
            'name' => ucfirst($group->handle).'Category',
            'interfaces' => [
                \markhuot\CraftQL\Types\Category::interface()
            ],
            'fields' => $fields,
        ]);
    }

    // static function baseFields() {
    //     if (!empty(static::$baseFields)) {
    //         return static::$baseFields;
    //     }

    //     $categoryGroup = new ObjectType([
    //         'name' => 'CategoryGroup',
    //         'fields' => [
    //             'id' => ['type' => Type::nonNull(Type::int())],
    //             'name' => ['type' => Type::nonNull(Type::string())],
    //             'handle' => ['type' => Type::nonNull(Type::string())],
    //         ],
    //     ]);

    //     $fields = [];
    //     $fields['id'] = ['type' => Type::nonNull(Type::int())];
    //     $fields['title'] = ['type' => Type::nonNull(Type::string())];
    //     $fields['slug'] = ['type' => Type::string()];
    //     $fields['uri'] = ['type' => Type::string()];
    //     $fields['group'] = ['type' => $categoryGroup, 'resolve' => function ($root, $args) {
    //         return $root->group;
    //     }];

    //     return static::$baseFields = $fields;
    // }

    // static function interface() {
    //     if (!static::$interface) {
    //         $fields = static::baseFields();

    //         static::$interface = new InterfaceType([
    //             'name' => 'Category',
    //             'description' => 'A category in Craft',
    //             'fields' => $fields,
    //             'resolveType' => function ($category) {
    //                 return ucfirst($category->group->handle);
    //             }
    //         ]);
    //     }

    //     return static::$interface;
    // }

}