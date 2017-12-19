<?php

namespace markhuot\CraftQL\Builders;

use craft\base\Field as CraftField;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ObjectType;
use markhuot\CraftQL\Request;
use markhuot\CraftQL\Builders\Field as BaseField;
use markhuot\CraftQL\Builders\Object as ObjectField;

class Schema implements \ArrayAccess {

    private $name;
    private $fields = [];
    private $reallyRawFields = [];
    protected $context;
    static $globals;
    protected $interfaces = [];
    static $singletons = [];
    protected $request;
    private $parent;

    function __construct(Request $request, $context=null, $parent=null) {
        $this->request = $request;
        $this->context = $context;
        $this->parent = $parent;
        // CALLED LOWER, BEFORE GETTING FIELDS TO ACCOUNT FOR
        // CIRCULAR REFERENCES
        // $this->boot();
        // $this->bootTraits();
    }

    /**
     * @TODO rename boot() to init() to better match Yii
     *
     * @return void
     */
    protected function boot() {
        /* intended to be overridden by subclassed schemas */
    }

    function bootTraits() {
        $traits = class_uses($this);
        foreach ($traits as $trait) {
            $reflect = new \ReflectionClass($trait);
            $method = 'boot'.$reflect->getShortName();
            if (method_exists($this, $method)) {
                $this->$method();
            }
        }
    }

    // function clone() {
    //     return new static($this->request, null, $this);
    // }

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

    function name(string $name): self {
        $this->name = $name;
        return $this;
    }

    function getName():string {
        if ($this->name === null) {
            $reflect = new \ReflectionClass(static::class);
            return $this->name = $reflect->getShortName();
        }

        return $this->name;
    }

    function addRawField($name): BaseField {
        return $this->fields[] = new BaseField($this->request, $name);
    }

    function addRawStringField($name): BaseField {
        return $this->fields[] = (new BaseField($this->request, $name))->type(Type::string());
    }

    function addRawIntField($name): BaseField {
        return $this->fields[] = (new BaseField($this->request, $name))->type(Type::int());
    }

    function addRawFloatField($name): BaseField {
        return $this->fields[] = (new BaseField($this->request, $name))->type(Type::float());
    }

    function addRawBooleanField($name): BaseField {
        return $this->fields[] = (new BaseField($this->request, $name))->type(Type::boolean());
    }

    function addRawDateField($name): BaseField {
        return $this->fields[] = new Date($this->request, $name);
    }

    function addRawEnumField($name): BaseField {
        return $this->fields[] = new Enum($this->request, $name);
    }

    function addRawObjectField(string $name, callable $config=null): ObjectField {
        return $this->fields[] = (new ObjectField($this->request, $name))
            ->config($config);
    }

    function addObjectField(CraftField $field, callable $config=null): ObjectField {
        return $this->fields[] = (new ObjectField($this->request, $field->handle))
            ->description($field->instructions)
            ->config($config);
    }

    function addField(CraftField $field): BaseField {
        return $this->fields[] = new ContentField($this->request, $field);
    }

    function addStringField(CraftField $field): BaseField {
        return $this->addField($field);
    }

    function addBooleanField(CraftField $field): BaseField {
        return $this->fields[] = (new Boolean($this->request, $field->handle))
            ->description($field->instructions);
    }

    function addEnumField(CraftField $field): BaseField {
        return $this->fields[] = (new Enum($this->request, $field->handle))
            ->description($field->instructions);
    }

    function addIntField(CraftField $field): BaseField {
        return $this->fields[] = (new ContentField($this->request, $field))
            ->type(Type::int());
    }

    function addFloatField(CraftField $field): BaseField {
        return $this->fields[] = (new ContentField($this->request, $field))
            ->type(Type::float());
    }

    function addDateField(CraftField $field): BaseField {
        return $this->fields[] = new Date($this->request, $field->handle);
    }

    function addUnionField(CraftField $field): BaseField {
        return $this->fields[] = new Union($this->request, $field);
    }

    function addFieldsByLayoutId(int $fieldLayoutId) {
        $fieldService = \Yii::$container->get('fieldService');
        $fields = $fieldService->getFields($fieldLayoutId, $this->request, $this);
        return $this->fields = array_merge($this->fields, $fields);
    }

    function getInterfaces(): array {
        return $this->interfaces;
    }

    function foo(): array {
        $interfaces = [];

        foreach ($this->getInterfaces() as $interface) {
            if (is_string($interface) && is_subclass_of($interface, Schema::class)) {
                $interfaces[] = ($interface::singleton($this->request));
            }

            else if (is_subclass_of($interface, Schema::class)) {
                $interfaces[] = $interface;
            }

            else {
                throw new \Exception('The interface is not a subclass of a known builder');
            }
        }

        return $interfaces;
    }

    function getRawInterfaces(): array {
        $interfaces = [];

        foreach ($this->getInterfaces() as $interface) {
            if (is_string($interface) && is_subclass_of($interface, Schema::class)) {
                $interfaces[] = ($interface::singleton($this->request))->getRawGraphQLObject();
            }

            else if (is_subclass_of($interface, Schema::class)) {
                $interfaces[] = $interface->getRawGraphQLObject();
            }

            else {
                $interfaces[] = $interface;
            }
        }

        return $interfaces;
    }

    function getRequest(): Request {
        return $this->request;
    }

    function getFields(): array {
        $this->boot();
        $this->bootTraits();
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

        foreach ($this->foo() as $interface) {
            foreach ($interface->getFields() as $field) {
                $fields[$field->getName()] = $field->getConfig();
            }
        }

        foreach ($this->getFields() as $field) {
            $fields[$field->getName()] = $field->getConfig();
        }

        $fields = array_merge($fields, $this->reallyRawFields);

        return $fields;
    }

    function getGraphQLConfig() {
        return [
            'name' => $this->getName(),
            'fields' => function () {
                return $this->getFieldConfig();
            },
            'interfaces' => $this->getRawInterfaces(),
            'resolveType' => $this->getResolveType(),
        ];
    }

    /**
     * Gets a function that will resolve an interface in to a valid type
     *
     * @return callable
     */
    function getResolveType() {
        return null;
    }

    static $objects;

    function getRawGraphQLObject() {
        $key = static::class;

        if (!empty(static::$objects[$key])) {
            return static::$objects[$key];
        }

        return static::$objects[$key] = $this->getGraphQLObject();
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