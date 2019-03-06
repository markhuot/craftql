<?php

namespace markhuot\CraftQL\FieldBehaviors;

use GraphQL\Type\Definition\ResolveInfo;
use markhuot\CraftQL\Behaviors\FieldBehavior;
use markhuot\CraftQL\Types\CropInputObject;
use markhuot\CraftQL\Types\NamedTransformsEnum;

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
