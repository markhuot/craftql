<?php

namespace markhuot\CraftQL\Builders;

use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Builders\ContentField;

class Boolean extends ContentField {

    function getConfig() {
        return [
            'type' => Type::boolean(),
        ];
    }

}