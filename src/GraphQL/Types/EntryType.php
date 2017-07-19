<?php

namespace markhuot\CraftQL\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;

class EntryType extends ObjectType {

    static $types = [];

    static function make($entryType) {
        $fieldService = \Yii::$container->get(\markhuot\CraftQL\Services\FieldService::class);

        $fields = \markhuot\CraftQL\GraphQL\Types\Entry::baseFields();
        $fields = array_merge($fields, $fieldService->getFields($entryType->fieldLayoutId));

        // var_dump($entryType->id);
        return static::$types[$entryType->id] = new static([
            'name' => static::getName($entryType),
            'fields' => $fields,
            'interfaces' => [
                \markhuot\CraftQL\GraphQL\Types\Entry::interface(),
                \markhuot\CraftQL\GraphQL\Types\Element::interface(),
            ],
        ]);
    }

    static function get($entryTypeId) {
        return @static::$types[$entryTypeId];
    }

    static function all() {
        return static::$types;
    }

    static function getName($entryType) {
        if ($entryType->section->handle == $entryType->handle) {
            return ucfirst($entryType->handle);
        }
        
        return ucfirst($entryType->section->handle).ucfirst($entryType->handle);
    }

}