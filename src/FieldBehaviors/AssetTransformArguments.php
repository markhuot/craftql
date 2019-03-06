<?php

namespace markhuot\CraftQL\FieldBehaviors;

use GraphQL\Type\Definition\ResolveInfo;
use markhuot\CraftQL\Behaviors\FieldBehavior;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Request;
use markhuot\CraftQL\Types\CropFormatInputEnum;
use markhuot\CraftQL\Types\CropinputObject;
use markhuot\CraftQL\Types\NamedTransformsEnum;
use markhuot\CraftQL\Types\PositionInputEnum;

class AssetTransformArguments extends FieldBehavior {

    function initAssetTransformArguments() {
        $this->owner->addStringArgument('transform')->type(NamedTransformsEnum::class);
        $this->owner->addStringArgument('crop')->type(CropInputObject::class);
        $this->owner->addStringArgument('fit')->type(CropInputObject::class);
        $this->owner->addStringArgument('stretch')->type(CropInputObject::class);

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
