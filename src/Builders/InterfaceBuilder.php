<?php

namespace markhuot\CraftQL\Builders;

use GraphQL\Type\Definition\InterfaceType;

class InterfaceBuilder extends Schema {

    function getGraphQLObject() {
        return new InterfaceType($this->getConfig());
    }

}