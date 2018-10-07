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
                return Utils::undefined();
//                return Executor::defaultFieldResolver($source, $args, $context, $info);
            }

            $field = $root->{$info->fieldName};

//            if ($this->isNonNull() && !$date) {
//                throw new Error("`{$info->fieldName}` is a required field but has no value");
//            }

            if (!$field) {
                return null;
            }

//            $date->setTimezone(new DateTimeZone($timezone));
//
//            $date = $date->format($format);
//            $cast = ($format === 'U') ? 'intval' : 'strval';
//            return $cast($date);

                // hard line
            if ($info->fieldName === $listField) {
                $field->where = 'elements.id not in (' . $inVals . ')';
//                $field->where = 'elements.id not in (20, 574, 733)';

//                $field->where = [
//                    $field->where = [
//                    'not' => [
//                        'elements.id' => [ 22, 574, 733 ]
//                    ]
//                ];
            }

            return $field;
        };
    }

// can't do any of this either. How about parent?
//    function getResolve()
//    {
////        if ($this instanceof ListOfType) {
//        if ($this->getIsList()) {
//            return Executor::defaultFieldResolver($source, $args, $context, $info);
//        } else {
//            return function ($source, $args, $context, $info) {
//
//                return false && is_null($source)
//                    ? null
//                    : Executor::defaultFieldResolver($source, $args, $context, $info);
//            };
//        }
//    }

//    function getResolve() {
//        return function ($root, $args, $context, $info) {
//            $ins = [];
//
//            if (isset($info->fieldNodes[0]->directives[0])) {
//                $directive = $info->fieldNodes[0]->directives[0];
//                if ($directive->arguments) {
//                    foreach ($directive->arguments as $arg) {
//                        switch ($arg->name->value) {
//                            case 'in':
//                                $ins = $arg->value->value;
//                                break;
//                        }
//                    }
//                }
//            } else {
////                if ($this->resolve !== null) {
////                    return function($value) {
////                        return $this->resolve;
////                    };
////                }
//                $children = $root
//                    ? $root->{$info->fieldName}
//                    : null;
//
//                return $children;
//            }
//
//            $children = $root
//                ? $root->{$info->fieldName}
//                : null;
//
////
////            if ($this->isNonNull() && !$date) {
////                throw new Error("`{$info->fieldName}` is a required field but has no value");
////            }
////
////            if (!$date) {
////                return null;
////            }
//
//
//            // here examine the id/arrayids of the returned entry vs. list
//            // if in list, return empty/null -- are we array here?
////            $date->setTimezone(new DateTimeZone($timezone));
////
////            $date = $date->format($format);
////            $cast = ($format === 'U') ? 'intval' : 'strval';
////            return $cast($date);
//            return $children;
//        };
//    }

}
