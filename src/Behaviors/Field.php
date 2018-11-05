<?php

namespace markhuot\CraftQL\Behaviors;

use GraphQL\Type\Definition\ResolveInfo;
use markhuot\CraftQL\Request;
use yii\base\Behavior;

class Field extends Behavior {

    function getCraftQLFieldType(Request $request, \craft\base\Field $field, $args, $context, ResolveInfo $info) {
        return get_class($field);
    }

    function getCraftQLSettings(Request $request, \craft\base\Field $field, $args, $context, ResolveInfo $info) {
        return json_encode($field->settings);
    }

}