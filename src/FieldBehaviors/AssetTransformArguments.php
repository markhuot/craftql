<?php

namespace markhuot\CraftQL\FieldBehaviors;

use GraphQL\Type\Definition\ResolveInfo;
use markhuot\CraftQL\Behaviors\FieldBehavior;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

class AssetTransformArguments extends FieldBehavior {

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
            $values[$transform->handle] = [
                'value' => $transform->handle,
                'description' => $transform->name
            ];
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
                'topLeft' => 'top-left',
                'topCenter' => 'top-center',
                'topRight' => 'top-right',
                'centerLeft' => 'center-left',
                'centerCenter' => 'center-center',
                'centerRight' => 'center-right',
                'bottomLeft' => 'bottom-left',
                'bottomCenter' => 'bottom-center',
                'bottomRight' => 'bottom-right',
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

    function initAssetTransformArguments() {
        $this->owner->addStringArgument('transform')->type(static::getTransformsEnum());
        $this->owner->addStringArgument('crop')->type(static::cropInputObject());
        $this->owner->addStringArgument('fit')->type(static::cropInputObject());
        $this->owner->addStringArgument('stretch')->type(static::cropInputObject());

        $this->owner->resolve(function ($root, $args, $context, ResolveInfo $info) {
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

            switch ($info->fieldName) {
                case 'url': return $root->getUrl($transform);
                case 'width': return $root->getWidth($transform);
                case 'height': return $root->getHeight($transform);
            }
        });
    }

}
