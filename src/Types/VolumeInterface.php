<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Builders\Schema;

class VolumeInterface extends Schema {

    static $transformEnum;
    static $positionInputEnum;
    static $formatInputEnum;
    static $cropInputObject;

    static function getTransformsEnum() {
        if (!empty(static::$transformEnum)) {
            return static::$transformEnum;
        }

        $values = [];

        foreach (\Craft::$app->getAssetTransforms()->getAllTransforms() as $transform) {
            $values[$transform->handle] = $transform->name;
        }

        if (empty($values)) {
            $values[] = 'Empty';
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

    function boot() {
        $this->addRawIntField('id');
        $this->addRawStringField('uri');
        $this->addRawStringField('url')
            ->arguments([
                'transform' => static::getTransformsEnum(),
                'crop' => static::cropInputObject(),
                'fit' => static::cropInputObject(),
                'stretch' => static::cropInputObject(),
            ])
            ->resolve(function ($root, $args) {
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
            });

        $this->addRawStringField('width');
        $this->addRawStringField('height');
        $this->addRawIntField('size');
        $this->addRawStringField('folder');
        $this->addRawStringField('mimeType');
        $this->addRawStringField('title');
        $this->addRawStringField('extension');
        $this->addRawStringField('filename');
        $this->addRawDateField('dateCreatedTimestamp');
        $this->addRawDateField('dateCreated');
        $this->addRawDateField('dateUpdatedTimestamp');
        $this->addRawDateField('dateUpdated');
    }

    function getGraphQLObject() {
        return new InterfaceType($this->getGraphQLConfig());
    }

    function getResolveType() {
        return function ($type) {
            return ucfirst($type->volume->handle).'Volume';
        };
    }

}