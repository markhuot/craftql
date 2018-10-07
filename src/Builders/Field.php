<?php

namespace markhuot\CraftQL\Builders;

use GraphQL\Type\Definition\Type;
use GraphQL\Executor\Executor;
use GraphQL\Utils\Utils;
use markhuot\CraftQL\Behaviors\FieldBehavior;
use markhuot\CraftQL\Request;

class Field extends BaseBuilder
{

    use HasTypeAttribute;
    use HasDescriptionAttribute;
    use HasIsListAttribute;
    use HasNonNullAttribute;
    use HasResolveAttribute;
    use HasArgumentsAttribute;
    use HasDeprecationReasonAttribute;

//    protected $arguments = [];

    function __construct(Request $request, string $name)
    {
        $this->request = $request;
        $this->name = $name;
        $this->boot();
    }

    protected function boot()
    {

    }

    /**
     * Add behaviors to our builder
     *
     * @param string $behavior
     * @return self
     */
    function use(FieldBehavior $behavior): self
    {
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
    function createInputObjectType($name): InputSchema
    {
        return new InputSchema($this->request, $name);
    }

    function getConfig()
    {
        $type = $this->getTypeConfig();

        if ($this->isList) {
            $type = Type::listOf($type);
        }

        if ($this->isNonNull) {
            $type = Type::nonNull($type);
        }

        // init behaviors
        if ($behaviors = $this->getBehaviors()) {
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

    function onSave(callable $callback)
    {
        $this->onSave = $callback;
    }

    function getOnSave()
    {
        return $this->onSave;
    }

    function getResolve() {

        // a critical alternative here, to stay out of the schema build
        // at present, Field is the place we want to hit for list directive
        return $this->resolve
            ? $this->resolve
            : function ($root, $args, $context, $info) {

            $inVals = '0';
            $listField = 'children';

            if (isset($info->fieldNodes[0]->directives[0])) {

                $directive = $info->fieldNodes[0]->directives[0];
                if ($directive->arguments) {
                    foreach ($directive->arguments as $arg) {
                        switch ($arg->name->value) {
                            case 'in':
                                $i = 0;
                                // tricky here - don't point to internal of values
                                foreach ($arg->value->values as $value) {
                                    $i++ === 0
                                        ? $inVals = $value->value
                                        : $inVals .= ', ' . $value->value;
                                }
                                break;

                            case 'field':
                                $listField = $arg->value->value;
                                break;
                        }
                    }
                }
            }

            if(is_null($root)) {
                return Utils::undefined(); // safety; shouldn't occur
            }

            $field = $root->{$info->fieldName};
            if (!$field) {
                return null; // safety; shouldn't occur
            }

            if ($info->fieldName === $listField) {
                // *todo* hard line only from here? alternate databases...
                $field->where = 'elements.id not in (' . $inVals . ')';
            }

            return $field;
        };
    }
}
