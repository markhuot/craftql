<?php

namespace markhuot\CraftQL\Builders;

class Directive {

    use HasDescriptionAttribute;
    use HasArgumentsAttribute;

    const FIELD = '1';

    private $name;
    static $objects = [];
    protected $locations = [];

    function __construct() {
    }

    function getName() {
        return 'Foo';
    }

    function getConfig() {
        return [
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'locations' => $this->getLocations(),
            'args' => $this->getArguments(),
        ];
    }

    function getLocations() {
        return $this->locations;
    }

    function getRawGraphQLObject(): \GraphQL\Type\Definition\Directive {
        $key = $this->getName();

        if (!empty(static::$objects[$key])) {
            return static::$objects[$key];
        }

        return static::$objects[$key] = $this->getGraphQLObject();
    }

    function getGraphQLObject() {
        return new \GraphQL\Type\Definition\Directive($this->getConfig());
    }

}