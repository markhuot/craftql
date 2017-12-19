<?php

namespace markhuot\CraftQL\Builders;

use craft\base\Field as CraftField;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Request;
use yii\base\Component;

class Field extends Component {

    protected $request;
    protected $name;
    protected $type;
    protected $description;
    protected $resolve;
    protected $isList = false;
    protected $isNonNull = false;
    protected $arguments = [];

    function __construct(Request $request, string $name) {
        $this->request = $request;
        $this->name = $name;
        $this->boot();
    }

    protected function boot() {

    }

    function getRequest() {
        return $this->request;
    }

    function name($name): self {
        $this->name = $name;
        return $this;
    }

    function getName() {
        return $this->name;
    }

    function type($type): self {
        $this->type = $type;
        return $this;
    }

    function getType() {
        return $this->type;
    }

    function getRawType() {
        $type = $this->getType();

        if (is_string($type) && is_subclass_of($type, Schema::class)) {
            return ($type::singleton($this->request))->getRawGraphQLObject();
        }

        else if (is_a($type, Schema::class) || is_subclass_of($type, Schema::class)) {
            return $type->getRawGraphQLObject();
        }

        return $type ?: Type::string();
    }

    function arguments($arguments): self {
        $this->arguments = $arguments;
        return $this;
    }

    function getArguments(): array {
        return $this->arguments;
    }

    function getConfig() {
        $type = $this->getRawType();

        if ($this->isList) {
            $type = Type::listOf($type);
        }

        if ($this->isNonNull) {
            $type = Type::nonNull($type);
        }

        // get behaviors
        if ($behaviors=$this->getBehaviors()) {
            foreach ($behaviors as $key => $behavior) {
                $this->{"init{$key}"}();
            }
        }

        return [
            'type' => $type,
            'description' => $this->getDescription(),
            'args' => $this->getArguments(),
            'resolve' => $this->getResolve(),
        ];
    }

    function use(string $behavior): self {
        $reflect = new \ReflectionClass($behavior);
        $this->attachBehavior($reflect->getShortName(), $behavior);
        return $this;
    }

    function description($description): self {
        $this->description = $description;
        return $this;
    }

    function getDescription() /* php 7.1: ?string*/ {
        return $this->description;
    }

    function lists($isList=true): self {
        $this->isList = $isList;
        return $this;
    }

    function nonNull(): self {
        $this->isNonNull = true;
        return $this;
    }

    function isNonNull(): bool {
        return $this->isNonNull;
    }

    function resolve($resolve): self {
        $this->resolve = $resolve;
        return $this;
    }

    function getResolve() /* php 7.1: ?callable*/ {
        if (is_callable($this->resolve)) {
            return $this->resolve;
        }

        if ($this->resolve !== null) {
            return function($root, $args) {
                return $this->resolve;
            };
        }

        return null;
    }

}