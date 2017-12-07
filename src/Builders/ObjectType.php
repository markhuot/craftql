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

    function toArray(): array {
        $export = [];

        foreach ($this->fields as $fieldName => $field) {
            if (is_a($field['type'], \markhuot\CraftQL\Builders\Enum::class)) {
                $field['type'] = $field['type']->toArray();
            }
            $export[$fieldName] = $field;
        }

        return $export;
    }

    function getArgs(): array {
        return $this->args;
    }

    function addCraftField(\craft\base\Field $field, $type, callable $resolve=null): self {
        return $this->addField($field->handle, [
            'type' => $type,
            'description' => $field->instructions,
            'resolve' => $resolve,
        ]);
    }

    function addStringField(\craft\base\Field $field, callable $resolve=null): self {
        return $this->addCraftField($field, Type::string(), $resolve);
    }

    function addBooleanField(\craft\base\Field $field, callable $resolve=null): self {
        return $this->addCraftField($field, Type::boolean(), $resolve);
    }

    function newEnum(string $name): Enum {
        return new Enum($name);
    }

    function addEnumField(\craft\base\Field $field, \markhuot\CraftQL\Builders\Enum $enum, callable $resolve=null): self {
        return $this->addCraftField($field, $enum, $resolve);
    }

    function addCraftArgument(\craft\base\Field $field, $type, callable $callback=null) {
        $this->args[$field->handle] = $type;

        if ($callback) {
            $this->mutationCallbacks[$field->handle] = $callback;
        }

        return $this;
    }

    function addStringArgument(\craft\base\Field $field, callable $callback=null) {
        return $this->addCraftArgument($field, Type::string(), $callback);
    }

    function addEnumArgument(\craft\base\Field $field, \markhuot\CraftQL\Builders\Enum $enum, callable $callback=null) {
        return $this->addCraftArgument($field, $enum->toArray(), $callback);
    }

    function addBooleanArgument(\craft\base\Field $field, callable $callback=null) {
        return $this->addCraftArgument($field, Type::boolean(), $callback);
    }

    function mutate($entry, \craft\base\Field $field, $value) {
        if (!empty($this->mutationCallbacks[$field->handle])) {
            $value = $this->mutationCallbacks[$field->handle]($entry, $field, $value);
        }

        return $value;
    }

}