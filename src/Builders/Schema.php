<?php

namespace markhuot\CraftQL\Builders;

use craft\base\Field as CraftField;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Request;
use markhuot\CraftQL\Builders\Field as BaseField;

class Schema {

    private $fields;
    static $globals;

    function __construct(Request $request) {
        $this->request = $request;
    }

    static function addGlobal($request, $callback) {
        if (!static::$globals) {
            static::$globals = new static($request);
        }

        $callback(static::$globals);
    }

    function addGlobalField($name) {
        return $this->fields[] = static::$globals->getField($name);
    }

    static function getGlobals() {
        return static::$globals;
    }

    function addRawField($name) {
        return $this->fields[] = new BaseField($this->request, $name);
    }

    function addRawStringField($name) {
        return $this->fields[] = (new BaseField($this->request, $name))->type(Type::string());
    }

    function addField(CraftField $field): BaseField {
        return $this->fields[] = new ContentField($this->request, $field);
    }

    function addStringField(CraftField $field): BaseField {
        return $this->addField($field);
    }

    function addBooleanField(CraftField $field): BaseField {
        return $this->fields[] = new Boolean($this->request, $field);
    }

    function addEnumField(CraftField $field): BaseField {
        return $this->fields[] = new Enum($this->request, $field);
    }

    function addDateField(CraftField $field): BaseField {
        return $this->fields[] = new Date($this->request, $field);
    }

    function getRequest() {
        return $this->request;
    }

    function getField($name) {
        foreach ($this->fields as $field) {
            if ($field->getName() == $name) {
                return $field;
            }
        }

        return false;
    }

    function config():array {
        $fields = [];

        foreach ($this->fields as $field) {
            $fields[$field->getName()] = $field->getConfig();
        }

        return $fields;
    }

    function args() {
        return [];
    }

    // function addCraftArgument(\craft\base\Field $field, $type, callable $callback=null) {
    //     $this->args[$field->handle] = $type;

    //     if ($callback) {
    //         $this->mutationCallbacks[$field->handle] = $callback;
    //     }

    //     return $this;
    // }

    // function addStringArgument(\craft\base\Field $field, callable $callback=null) {
    //     return $this->addCraftArgument($field, Type::string(), $callback);
    // }

    // function addEnumArgument(\craft\base\Field $field, \markhuot\CraftQL\Builders\Enum $enum, callable $callback=null) {
    //     return $this->addCraftArgument($field, $enum->toArray(), $callback);
    // }

    // function addBooleanArgument(\craft\base\Field $field, callable $callback=null) {
    //     return $this->addCraftArgument($field, Type::boolean(), $callback);
    // }

    // function mutate($entry, \craft\base\Field $field, $value) {
    //     if (!empty($this->mutationCallbacks[$field->handle])) {
    //         $value = $this->mutationCallbacks[$field->handle]($entry, $field, $value);
    //     }

    //     return $value;
    // }

}