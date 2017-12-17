<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;

class Category extends ObjectType {

    static $interface;
    static $baseFields;

    function __construct($group, $request) {
        $fieldService = \Yii::$container->get('fieldService');
        $fields = array_merge(\markhuot\CraftQL\Types\Category::baseFields($request), $fieldService->getFields($group->fieldLayoutId, $request));

        parent::__construct([
            'name' => ucfirst($group->handle).'Category',
            'interfaces' => [
                \markhuot\CraftQL\Types\Category::interface($request)
            ],
            'fields' => $fields,
            'id' => $group->id,
        ]);
    }

    static function baseFields($request) {
        if (!empty(static::$baseFields)) {
            return static::$baseFields;
        }

        $fields = [];
        $fields['id'] = ['type' => Type::nonNull(Type::int())];
        $fields['title'] = ['type' => Type::nonNull(Type::string())];
        $fields['slug'] = ['type' => Type::string()];
        $fields['uri'] = ['type' => Type::string()];
        $fields['group'] = ['type' => \markhuot\CraftQL\Types\CategoryGroup::singleton($request)];

        return static::$baseFields = $fields;
    }

    static function interface($request) {
        if (!static::$interface) {
            $fields = static::baseFields($request);

            static::$interface = new InterfaceType([
                'name' => 'CategoryInterface',
                'description' => 'A category in Craft',
                'fields' => $fields,
                'resolveType' => function ($category) {
                    return ucfirst($category->group->handle).'Category';
                }
            ]);
        }

        return static::$interface;
    }

    static function args($request) {
        return [
            'ancestorOf' => Type::int(),
            'ancestorDist' => Type::int(),
            'level' => Type::int(),
            'descendantOf' => Type::int(),
            'descendantDist' => Type::int(),
            'fixedOrder' => Type::boolean(),
            // 'group' => $this->request->categoryGroups()->enum(),
            'groupId' => Type::int(),
            'id' => Type::int(),
            'indexBy' => Type::string(),
            'limit' => Type::int(),
            'locale' => Type::string(),
            'nextSiblingOf' => Type::int(),
            'offset' => Type::int(),
            'order' => Type::string(),
            'positionedAfter' => Type::int(),
            'positionedBefore' => Type::int(),
            'prevSiblingOf' => Type::int(),
            'relatedTo' => Type::listOf(Entry::relatedToInputObject()),
            'search' => Type::string(),
            'siblingOf' => Type::int(),
            'slug' => Type::string(),
            'title' => Type::string(),
            'uri' => Type::string(),
        ];
    }

}