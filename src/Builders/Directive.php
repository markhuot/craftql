<?php

namespace markhuot\CraftQL\Builders;

use markhuot\CraftQL\Request;

class Directive {

    use HasNameAttribute;
    use HasArgumentsAttribute;
    use HasDescriptionAttribute;

    /** @var Request */
    protected $request;

    /** @var \GraphQL\Type\Definition\Directive[] */
    static $directives = [];

    /** @var string[] */
    protected $locations = [];

    /**
     * Directive constructor.
     */
    function __construct(Request $request) {
        $this->request = $request;
    }

    function addLocation($location) {
        $this->locations[] = $location;
        return $this;
    }

    function getLocations() {
        return $this->locations;
    }

    function boot() {

    }

    function getConfig() {
        $this->boot();

        return [
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'locations' => $this->getLocations(),
            'args' => $this->getDirectiveArgumentConfig(),
        ];
    }

    function getRawGraphQLObject() {
        $key = $this->getName();

        if (!empty(static::$directives[$key])) {
            return static::$directives[$key];
        }

        return static::$directives[$key] = $this->getGraphQLObject();
    }

    function getGraphQLObject() {
        return new \GraphQL\Type\Definition\Directive($this->getConfig());
    }

}