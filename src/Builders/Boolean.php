<?php

namespace markhuot\CraftQL\Builders;

use GraphQL\Type\Definition\Type;

class Boolean extends Field {

    function getType() {
        return Type::boolean();
    }

}