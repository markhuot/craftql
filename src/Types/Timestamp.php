<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Error\Error;
use GraphQL\Error\InvariantViolation;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Utils\Utils;
use markhuot\CraftQL\Builders\Schema;

class Timestamp extends Schema {

    function getGraphQLObject() {
        return new \markhuot\CraftQL\Scalars\Timestamp();
    }

}