<?php

namespace markhuot\CraftQL\Builders;

use GraphQL\Type\Definition\InputObjectType;
use markhuot\CraftQL\Behaviors\FieldBehavior;

class InputSchema extends BaseBuilder {

    use HasNameAttribute;
    use HasArgumentsAttribute;

    protected static $objects;

    function __construct($request, $name=null) {
        $this->request = $request;
        $this->name = $name;
    }

    /**
     * Add behaviors to our builder
     *
     * @param string $behavior
     * @return self
     */
    function use(FieldBehavior $behavior): self {
        $reflect = new \ReflectionClass($behavior);
        $this->attachBehavior($reflect->getShortName(), $behavior);
        return $this;
    }

    /**
     * Create a new builder
     *
     * @param [type] $name
     * @return self
     */
    function createInputObjectType($name): InputSchema {
        $inputSchema = new InputSchema($this->request, $name);
        $this->request->registerType($name, $inputSchema);
        return $inputSchema;
    }

    function boot() {

    }

    function getArguments() {
        $this->boot();
        $this->bootBehaviors();
        return $this->getArgumentConfig();
    }

    function getGraphQLConfig() {
        return [
            'name' => $this->getName(),
            'fields' => function () {
                return $this->getArguments();
            },
        ];
    }

    function getRawGraphQLObject() {
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