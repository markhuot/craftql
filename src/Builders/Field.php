<?php

namespace markhuot\CraftQL\Builders;

use craft\base\Field as CraftField;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Request;

class Field {

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
        return $this->type ?: Type::string();
    }

    function arguments($arguments): self {
        $this->arguments = $arguments;
        return $this;
    }

    function getArguments(): array {
        return $this->arguments;
    }

    function getConfig() {
        $type = $this->getType();

        if ($this->isList) {
            $type = Type::listOf($type);
        }

        if ($this->isNonNull) {
            $type = Type::nonNull($type);
        }

        return [
            'type' => $type,
            'description' => $this->getDescription(),
            'args' => $this->getArguments(),
            'resolve' => $this->getResolve(),
        ];
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

    function resolve($resolve): self {
        $this->resolve = $resolve;
        return $this;
    }

    function getResolve() /* php 7.1: ?callable*/ {
        return $this->resolve;
    }

}