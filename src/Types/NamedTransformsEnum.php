<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\Builders\EnumObject;

class NamedTransformsEnum extends EnumObject {

    function getValues() {
        $values = [];

        foreach (\Craft::$app->getAssetTransforms()->getAllTransforms() as $transform) {
            $values[$transform->handle] = [
                'value' => $transform->handle,
                'description' => $transform->name
            ];
        }

        if (empty($values)) {
            $values['empty'] = 'Empty';
        }

        return $values;
    }

}