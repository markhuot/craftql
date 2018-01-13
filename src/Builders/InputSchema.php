<?php

namespace markhuot\CraftQL\Builders;

use GraphQL\Type\Definition\InputObjectType;

class InputSchema extends Schema {

    function getGraphQLObject() {
        return new InputObjectType($this->getConfig());
    }

}