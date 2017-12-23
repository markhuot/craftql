<?php

namespace markhuot\CraftQL\Builders;

use craft\base\Field as CraftField;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ObjectType;
use markhuot\CraftQL\Request;
use markhuot\CraftQL\Builders\Field as BaseField;
use markhuot\CraftQL\Builders\Object as ObjectField;
// use yii\base\Component;

class Schema extends BaseBuilder {

    protected static $objects;
    protected $fields = [];
    protected $context;
    protected $interfaces = [];
    protected $parent;

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

    /**
     * Get any context used to create this schema
     *
     * @return void
     */
    function getContext() {
        return $this->context;
    }

    /**
     * Create a new builder
     *
     * @param [type] $name
     * @return self
     */
    function createObjectType($name): self {
        return (new static($this->request))
            ->name($name);
    }

    function addObjectField(CraftField $field, callable $config=null): ObjectField {
        if (is_a($field, CraftField::class)) {
            return $this->fields[] = (new ObjectField($this->request, $field->handle))
                ->description($field->instructions)
                ->config($config);
        }

        return $this->fields[] = (new ObjectField($this->request, $field))
            ->config($config);
    }

    /**
     * Add a new field to this schema
     *
     * @param mixed $field
     * @return BaseField
     */
    function addField($field): BaseField {
        if (is_a($field, CraftField::class)) {
            return $this->fields[] = (new Field($this->request, $field->handle))
                ->description($field->instructions);
        }

        return $this->fields[] = new Field($this->request, $field);
    }

    /**
     * Sugar since `addField` defaults to a string anyway
     *
     * @param mixed $field
     * @return BaseField
     */
    function addStringField($field): BaseField {
        return $this->addField($field);
    }

    function addBooleanField($field): BaseField {
        return $this->addField($field)->type(Type::boolean());
    }

    function addIntField($field): BaseField {
        return $this->addField($field)->type(Type::int());
    }

    function addFloatField($field): BaseField {
        return $this->addField($field)->type(Type::float());
    }

    function addEnumField($field): BaseField {
        if (is_a($field, CraftField::class)) {
            return $this->fields[] = (new EnumField($this->request, $field->handle))
                ->description($field->instructions);
        }

        return $this->fields[] = new EnumField($this->request, $field);
    }

    function addDateField($field): BaseField {
        if (is_a($field, CraftField::class)) {
            return $this->fields[] = (new Date($this->request, $field->handle))
                ->description($field->instructions);
        }

        return $this->fields[] = new Date($this->request, $field);
    }

    function addUnionField($field): BaseField {
        if (is_a($field, CraftField::class)) {
            return $this->fields[] = (new Union($this->request, $field->handle))
                ->description($field->instructions);
        }

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
                $interfaces[] = (new $interface($this->request));
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
                $interfaces[] = (new $interface($this->request))->getRawGraphQLObject();
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

    function getFields(): array {
        $this->boot();
        $this->bootBehaviors();
        return $this->fields;
    }

    function getField($name): BaseField {
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

    function getRawGraphQLObject() {
        $key = $this->getName();

        if (!empty(static::$objects[$key])) {
            return static::$objects[$key];
        }

        return static::$objects[$key] = $this->getGraphQLObject();
    }

    function getGraphQLObject() {
        return new ObjectType($this->getGraphQLConfig());
    }

}