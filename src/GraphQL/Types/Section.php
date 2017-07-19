<?php

namespace markhuot\CraftQL\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;

class Section {

    static function make($section) {
        // $fields = \markhuot\CraftQL\GraphQL\Types\Entry::baseFields();

        // $fieldService = \Yii::$container->get(\markhuot\CraftQL\Services\FieldService::class);
        // foreach ($section->entryTypes as $entryType) {
        //     // $fields = array_merge($fields, $fieldService->getFields($entryType->fieldLayoutId));
        // }

        // $sectionType = new static([
        //     'name' => ucfirst($section->handle).'Section',
        //     'fields' => $fields,
        //     // 'interfaces' => [
        //     //     \markhuot\CraftQL\GraphQL\Types\Entry::interface(),
        //     //     \markhuot\CraftQL\GraphQL\Types\Element::interface(),
        //     // ],
        //     'resolveType' => function ($entry) {
        //         return \markhuot\CraftQL\GraphQL\Types\EntryType::getName($entry->type);
        //     },
        //     'type' => $section->type, 
        // ]);

        $entryTypes = [];
        foreach ($section->entryTypes as $entryType) {
            $entryTypes[] = \markhuot\CraftQL\GraphQL\Types\EntryType::make($entryType);
        }

        // $sectionType->config['entryTypes'] = $entryTypes;

        // return $sectionType;
    }

    static function args() {
        return [
            'after' => Type::string(),
            'ancestorOf' => Type::int(),
            'ancestorDist' => Type::int(),
            'archived' => Type::boolean(),
            'authorGroup' => Type::string(),
            'authorGroupId' => Type::int(),
            'authorId' => Type::int(),
            'before' => Type::string(),
            'level' => Type::int(),
            'localeEnabled' => Type::boolean(),
            'descendantOf' => Type::int(),
            'descendantDist' => Type::int(),
            'fixedOrder' => Type::boolean(),
            'id' => Type::int(),
            'limit' => Type::int(),
            'locale' => Type::string(),
            'nextSiblingOf' => Type::int(),
            'offset' => Type::int(),
            'order' => Type::string(),
            'positionedAfter' => Type::id(),
            'positionedBefore' => Type::id(),
            'postDate' => Type::string(),
            'prevSiblingOf' => Type::id(),
            'relatedTo' => Type::id(),
            'search' => Type::string(),
            'section' => Type::string(),
            'siblingOf' => Type::int(),
            'slug' => Type::string(),
            'status' => Type::string(),
            'title' => Type::string(),
            'type' => Type::string(),
            'uri' => Type::string(),
        ];
    }

}