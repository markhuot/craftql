<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;

class Section extends ObjectType {

    static $type;
    static $baseFields;
    static $baseArgs;

    static function bootstrap() {
        
    }

    function __construct($section, $request) {
        parent::__construct([
            'name' => ucfirst($section->handle).'Section',
            'description' => 'A section in Craft',
            'fields' => [
                'foo' => ['type' => Type::string()]
            ],
            'id' => $section->id
        ]);
    }

    static function baseInputArgs() {
        return [
            'id' => ['type' => Type::int()],
            'authorId' => ['type' => Type::int()],
            'title' => ['type' => Type::string()],
        ];
    }

    static function args($token=false) {
        if (!empty(static::$baseArgs)) {
            return static::$baseArgs;
        }

        return static::$baseArgs = [];
    }

    static function baseFields() {
        if (!empty(static::$baseFields)) {
            return static::$baseFields;
        }

        return static::$baseFields = [
            'id' => ['type' => Type::nonNull(Type::int())],
            'structureId' => ['type' => Type::int()],
            'name' => ['type' => Type::nonNull(Type::string())],
            'handle' => ['type' => Type::nonNull(Type::string())],
            'type' => ['type' => Type::nonNull(Type::string())],
            'template' => ['type' => Type::string()],
            'maxLevels' => ['type' => Type::int()],
            'hasUrls' => ['type' => Type::boolean()],
            'enableVersioning' => ['type' => Type::boolean()],
        ];
    }

    static function type() {
        if (!empty(static::$type)) {
            return static::$type;
        }

        return static::$type = new ObjectType([
            'name' => 'Section',
            'description' => 'A section in Craft',
            'fields' => static::baseFields(),
        ]);
    }

}