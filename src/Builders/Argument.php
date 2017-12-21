<?php

namespace markhuot\CraftQL\Builders;

use GraphQL\Type\Definition\Type;

class Argument {

    protected $name;
    protected $type;
    protected $isList = false;
    protected $isNonNull = false;

    function __construct($name) {
        $this->name = $name;
    }

    function name(string $name): self {
        $this->name = $name;
        return $this;
    }

    function getName(): string {
        return $this->name;
    }

    function type(Type $type): self {
        $this->type = $type;
        return $this;
    }

    function getType(): Type {
        return $this->type ?: Type::string();
    }

    function nonNull(bool $nonNull=true): self {
        $this->isNonNull = $nonNull;
        return $this;
    }

    function isNonNull(): bool {
        return $this->isNonNull;
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
        ];
    }

    function lists(bool $isList=true): self {
        $this->isList = $isList;
        return $this;
    }

}