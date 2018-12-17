<?php

namespace markhuot\CraftQL\Builders;

use GraphQL\Type\Definition\InterfaceType as BaseInterfaceType;

class InterfaceType extends ObjectType {

    function getGraphQLObject() {
        return new BaseInterfaceType($this->getConfig());
    }

}