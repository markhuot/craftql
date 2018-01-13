<?php

namespace markhuot\CraftQL\Builders;

use GraphQL\Type\Definition\InputObjectType;

class InputSchema extends BaseBuilder {

    use HasArgumentsAttribute;

    protected static $objects;

    function __construct($request, $name) {
        $this->request = $request;
        $this->name = $name;
    }

    function getGraphQLConfig() {
        return [
            'name' => $this->getName(),
            'fields' => function () {
                return $this->getArgumentConfig();
            },
        ];
    }

    function getRawGraphQLType() {
        $key = $this->getName();

        if (!empty(static::$objects[$key])) {
            return static::$objects[$key];
        }

        return static::$objects[$key] = $this->getGraphQLType();
    }

    function getGraphQLType() {
        return new InputObjectType($this->getGraphQLConfig());
    }

}