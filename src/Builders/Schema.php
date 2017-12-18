<?php

namespace markhuot\CraftQL\Builders;

use craft\base\Field as CraftField;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ObjectType;
use markhuot\CraftQL\Request;
use markhuot\CraftQL\Builders\Field as BaseField;

class Schema implements \ArrayAccess {

    private $name;
    private $fields = [];
    private $reallyRawFields = [];
    protected $context;
    static $globals;
    protected $interfaces = [];
    static $singletons = [];

    function __construct(Request $request, $context=null) {
        $this->request = $request;
        $this->context = $context;
        $this->boot();
    }

    protected function boot() {

    }

    static function singleton(Request $request, $key=null) {
        if ($key === null) {
            $key = static::class;
        }

        if (!empty(self::$singletons[$key])) {
            return self::$singletons[$key];
        }

        return self::$singletons[$key] = new static($request);
    }

    function getContext() {
        return $this->context;
    }

    function name(string $bane) {
        $this->name = $name;
    }

    function getName():string {
        if ($this->name === null) {
            $reflect = new \ReflectionClass(static::class);
            return $this->name = $reflect->getShortName();
        }

        return $this->name;
    }

    static function addGlobalFields($request, $callback) {
        if (!static::$globals) {
            static::$globals = new static($request);
        }

        $callback->apply(static::$globals);
    }

    function addGlobalField($name) {
        return $this->fields[] = static::$globals->getField($name);
    }

    static function getGlobals() {
        return static::$globals;
    }

    // @TODO remove this when it's no longer used
    function addReallyRawField($name, $field) {
        $this->reallyRawFields[$name] = $field;
    }

    function addRawField($name) {
        return $this->fields[] = new BaseField($this->request, $name);
    }

    function addRawStringField($name) {
        return $this->fields[] = (new BaseField($this->request, $name))->type(Type::string());
    }

    function addRawIntField($name) {
        return $this->fields[] = (new BaseField($this->request, $name))->type(Type::int());
    }

    function addRawFloatField($name) {
        return $this->fields[] = (new BaseField($this->request, $name))->type(Type::float());
    }

    function addRawBooleanField($name) {
        return $this->fields[] = (new BaseField($this->request, $name))->type(Type::boolean());
    }

    function addRawDateField($name): BaseField {
        return $this->fields[] = new Date($this->request, $name);
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
        return $this->fields[] = new Date($this->request, $field->handle);
    }

    function addUnionField(CraftField $field): BaseField {
        return $this->fields[] = new Union($this->request, $field);
    }

    function addFieldsByLayoutId(int $fieldLayoutId) {
        $fieldService = \Yii::$container->get('fieldService');
        $fields = $fieldService->getFields($fieldLayoutId, $this->request)['schema'];
        return $this->fields = array_merge($this->fields, $fields->getFields());
    }

    function getInterfaces(): array {
        return $this->interfaces;
    }

    function getRawInterfaces(): array {
        $interfaces = [];

        foreach ($this->getInterfaces() as $interface) {
            if (is_string($interface) && is_subclass_of($interface, Schema::class)) {
                $interfaces[] = ($interface::singleton($this->request))->getGraphQLObject();
            }

            else if (is_subclass_of($interface, Schema::class)) {
                $interfaces[] = $interface->getGraphQLObject();
            }

            else {
                $interfaces[] = $interface;
            }
        }

        return $interfaces;
    }

    function getRequest() {
        return $this->request;
    }

    function getFields(): array {
        return $this->fields;
    }

    function getField($name) {
        foreach ($this->fields as $field) {
            if ($field->getName() == $name) {
                return $field;
            }
        }

        return false;
    }

    function getFieldConfig():array {
        $fields = [];

        foreach ($this->getFields() as $field) {
            $fields[$field->getName()] = $field->getConfig();
        }

        $fields = array_merge($fields, $this->reallyRawFields);

        return $fields;
    }

    function getGraphQLConfig() {
        return [
            'name' => $this->getName(),
            'fields' => $this->getFieldConfig(),
            'interfaces' => $this->getRawInterfaces(),
        ];
    }

    function getGraphQLObject() {
        return new ObjectType($this->getGraphQLConfig());
    }

    function offsetExists($offset) {
        return isset($this->fields[$offset]);
    }

    function offsetGet($offset) {
        return $this->fields[$offset];
    }

    function offsetSet($offset , $value) {
        $this->fields[$offset] = $value;
    }

    function offsetUnset($offset) {
        unset($this->fields[$offset]);
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