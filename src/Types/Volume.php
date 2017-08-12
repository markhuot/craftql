<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;

class Volume extends ObjectType {

    static $baseFields;
    static $interface;

    function __construct($volume, $token) {
        $fieldService = \Yii::$container->get(\markhuot\CraftQL\Services\FieldService::class);
        $fields = array_merge(static::baseFields(), $fieldService->getFields($volume->fieldLayoutId, $token));

        parent::__construct([
            'name' => ucfirst($volume->handle).'Assets',
            'fields' => $fields,
            'interfaces' => [
                static::interface(),
            ],
        ]);
    }

    static function baseFields() {
        if (!empty(static::$baseFields)) {
            return static::$baseFields;
        }

        $fields = [];
        $fields['id'] = ['type' => Type::int()];
        $fields['uri'] = ['type' => Type::string()];
        $fields['url'] = ['type' => Type::string()];
        $fields['width'] = ['type' => Type::string()];
        $fields['height'] = ['type' => Type::string()];
        $fields['size'] = ['type' => Type::int()];
        $fields['folder'] = ['type' => Type::string()];
        $fields['mimeType'] = ['type' => Type::string()];
        $fields['title'] = ['type' => Type::string()];
        $fields['extension'] = ['type' => Type::string()];
        $fields['filename'] = ['type' => Type::string()];
        $fields['dateCreatedTimestamp'] = ['type' => Type::nonNull(Type::int()), 'resolve' => function ($root, $args) {
            return $root->dateCreated->format('U');
        }];
        $fields['dateCreated'] = ['type' => Type::nonNull(Type::string()), 'args' => [
            ['name' => 'format', 'type' => Type::string(), 'defaultValue' => 'r']
        ], 'resolve' => function ($root, $args) {
            return $root->dateCreated->format($args['format']);
        }];
        $fields['dateUpdatedTimestamp'] = ['type' => Type::nonNull(Type::int()), 'resolve' => function ($root, $args) {
            return $root->dateUpdated->format('U');
        }];
        $fields['dateUpdated'] = ['type' => Type::nonNull(Type::int()), 'args' => [
            ['name' => 'format', 'type' => Type::string(), 'defaultValue' => 'r']
        ], 'resolve' => function ($root, $args) {
            return $root->dateUpdated->format($args['format']);
        }];

        return static::$baseFields = $fields;
    }

    static function interface() {
        return static::$interface ?: static::$interface = new InterfaceType([
            'name' => 'AssetInterface',
            'description' => 'An asset in Craft',
            'fields' => static::baseFields(),
            'resolveType' => function ($asset) {
                return ucfirst($asset->getVolume()->handle).'Assets';
            }
        ]);
    }

}