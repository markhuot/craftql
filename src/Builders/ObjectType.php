<?php

namespace markhuot\CraftQL\Builders;

use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Builders\Enum;

class ObjectType {

    private $fields = [];
    private $args = [];
    private $mutationCallbacks = [];

    function addField($name, array $config=[]): self {
        $this->fields[$name] = $config;
        return $this;
    }

    function getFields(): array {
        return $this->fields;
    }

    function getArgs(): array {
        return $this->args;
    }

    function addStringField(\craft\base\Field $field, callable $resolve=null): self {
        return $this->addField($field->handle, [
            'type' => Type::string(),
            'description' => $field->instructions,
            'resolve' => $resolve,
        ]);
    }

    function newEnum(string $name): Enum {
        return new Enum($name);
    }

    function addEnumField(\craft\base\Field $field, \markhuot\CraftQL\Builders\Enum $enum, callable $resolve=null): self {
        return $this->addField($field->handle, [
            'type' => $enum->toArray(),
            'description' => $field->instructions,
            'resolve' => $resolve,
        ]);
    }

    function addStringMutation(\craft\base\Field $field, callable $callback=null) {
        $this->args[$field->handle] = ['type' => Type::string()];

        if ($callback) {
            $this->mutationCallbacks[$field->handle] = $callback;
        }
    }

    function addEnumMutation(\craft\base\Field $field, \markhuot\CraftQL\Builders\Enum $enum, callable $callback=null) {
        $this->args[$field->handle] = $enum->toArray();

        if ($callback) {
            $this->mutationCallbacks[$field->handle] = $callback;
        }
    }

    function mutate(\craft\base\Field $field, $value) {
        if (!empty($this->mutationCallbacks[$field->handle])) {
            $value = $this->mutationCallbacks[$field->handle]($value);
        }

        return $value;
    }

}