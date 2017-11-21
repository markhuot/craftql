<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;

class Volume extends ObjectType {

    static $baseFields;
    static $interface;
    static $transformEnum;
    static $cropInputObject;
    static $positionInputEnum;
    static $formatInputEnum;

    function __construct($volume, $token) {
        $fieldService = \Yii::$container->get(\markhuot\CraftQL\Services\FieldService::class);
        $fields = array_merge(static::baseFields(), $fieldService->getFields($volume->fieldLayoutId, $token));

        parent::__construct([
            'name' => ucfirst($volume->handle).'Assets',
            'fields' => $fields,
            'interfaces' => [
                static::interface(),
            ],
            'id' => $volume->id,
        ]);
    }

    static function getTransformsEnum() {
        if (!empty(static::$transformEnum)) {
            return static::$transformEnum;
        }

        $values = [];

        foreach (\Craft::$app->getAssetTransforms()->getAllTransforms() as $transform) {
            $values[$transform->handle] = $transform->name;
        }

        return static::$transformEnum = new EnumType([
            'name' => 'NamedTransformsEnum',
            'values' => $values,
        ]);
    }

    static function positionInputEnum() {
        if (!empty(static::$positionInputEnum)) {
            return static::$positionInputEnum;
        }

        return static::$positionInputEnum = new EnumType([
            'name' => 'PositionInputEnum',
            'values' => [
                'topLeft' => 'Top Left',
                'topCenter' => 'Top Center',
                'topRight' => 'Top Right',
                'centerLeft' => 'Center Left',
                'centerCenter' => 'Center Center',
                'centerRight' => 'Center Right',
                'bottomLeft' => 'Bottom Left',
                'bottomCenter' => 'Bottom Center',
                'bottomRight' => 'Bottom Right',
            ],
        ]);
    }

    static function formatInputEnum() {
        if (!empty(static::$formatInputEnum)) {
            return static::$formatInputEnum;
        }

        return static::$formatInputEnum = new EnumType([
            'name' => 'CropFormatInputEnum',
            'values' => [
                'jpg' => 'JPG',
                'gif' => 'GIF',
                'png' => 'PNG',
                'Auto' => 'Auto',
            ],
        ]);
    }

    static function cropInputObject() {
        if (!empty(static::$cropInputObject)) {
            return static::$cropInputObject;
        }

        return static::$cropInputObject = new InputObjectType([
            'name' => 'CropInputObject',
            'fields' => [
                'width' => ['type' => Type::int()],
                'height' => ['type' => Type::int()],
                'quality' => ['type' => Type::int()],
                'position' => ['type' => static::positionInputEnum()],
                'format' => ['type' => static::formatInputEnum()],
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
        $fields['url'] = [
            'type' => Type::string(),
            'args' => [
                'transform' => static::getTransformsEnum(),
                'crop' => static::cropInputObject(),
                'fit' => static::cropInputObject(),
                'stretch' => static::cropInputObject(),
            ],
            'resolve' => function ($root, $args) {
                if (!empty($args['transform'])) {
                    $transform = $args['transform'];
                }
                else if (!empty($args['crop'])) {
                    $transform = $args['crop'];
                    $transform['mode'] = 'crop';
                }
                else if (!empty($args['fit'])) {
                    $transform = $args['fit'];
                    $transform['mode'] = 'fit';
                }
                else if (!empty($args['stretch'])) {
                    $transform = $args['stretch'];
                    $transform['mode'] = 'stretch';
                }
                else {
                    $transform = null;
                }
                return $root->getUrl($transform);
            },
        ];
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