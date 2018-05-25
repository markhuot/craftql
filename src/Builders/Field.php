<?php

namespace markhuot\CraftQL\Builders;

use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Behaviors\FieldBehavior;
use markhuot\CraftQL\Request;

class Field extends BaseBuilder {

    use HasTypeAttribute;
    use HasDescriptionAttribute;
    use HasIsListAttribute;
    use HasNonNullAttribute;
    use HasResolveAttribute;
    use HasArgumentsAttribute;
    use HasDeprecationReasonAttribute;

//    protected $arguments = [];

    function __construct(Request $request, string $name) {
        $this->request = $request;
        $this->name = $name;
        $this->boot();
    }

    protected function boot() {

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
        return new InputSchema($this->request, $name);
    }

    function getConfig() {
        $type = $this->getTypeConfig();

        if ($this->isList) {
            $type = Type::listOf($type);
        }

        if ($this->isNonNull) {
            $type = Type::nonNull($type);
        }

        // init behaviors
        if ($behaviors=$this->getBehaviors()) {
            foreach ($behaviors as $key => $behavior) {
                $this->{"init{$key}"}();
            }
        }

        return [
            'type' => $type,
            'description' => $this->getDescription(),
            'args' => $this->getArgumentConfig(),
            'resolve' => $this->getResolve(),
            'deprecationReason' => $this->getDeprecationReason(),
        ];
    }

    function onSave(callable $callback) {
        $this->onSave = $callback;
    }

    function getOnSave() {
        return $this->onSave;
    }

}