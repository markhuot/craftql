<?php

namespace markhuot\CraftQL\Builders;

use GraphQL\Type\Definition\Type;
use GraphQL\Executor\Executor;
use GraphQL\Utils\Utils;
use markhuot\CraftQL\Behaviors\FieldBehavior;
use markhuot\CraftQL\Request;
use craft\helpers\Db;

class Field extends BaseBuilder
{

    use HasTypeAttribute;
    use HasDescriptionAttribute;
    use HasIsListAttribute;
    use HasNonNullAttribute;
    use HasResolveAttribute;
    use HasArgumentsAttribute;
    use HasDeprecationReasonAttribute;

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

    /**
     * @return \Closure|mixed
     */
    function getResolve()
    {
        // A critical alternative here, to stay out of the schema build.
        // The anonymous function is needed because PHP, but cleaner to see
        return $this->resolve
            ? $this->resolve
            : function($root, $args, $context, $info) {
                return $this->listResolve($root, $args, $context, $info);
            };
    }

    public function listResolve($root, $args, $context, $info)
    {

        // At present, Field is the Builder we want to hit, for
        // directives which appear on fields which are actually lists,
        // which at present include entries, and several more.

        // first, safeties, for what shoudldn't occur
        if (is_null($root)) {
            return Utils::undefined();
        }

        $field = $root->{$info->fieldName};
        if (!$field) {
            return null;
        }

        // now, handle each possible directive, first assessing their arguments

        if (isset($info->fieldNodes[0]->directives[0])) {
            foreach ($info->fieldNodes[0]->directives as $directive) {
                if ($directive->arguments) {
                    foreach ($directive->arguments as $arg) {
                        $argValues = $this->valuesFromArguments($info, $arg, $directive);

                        switch ($directive->name->value) {

                            case 'idNot':
                                $field = $this->handleAtIdNot($field, $argValues, $directive, $arg);
                                break;

                            // other field directives could be added here, with handler...

                            default:
                                break;
                        }
                    }
                }
            }
        }

        return $field;
    }

    /**
     * @param $info
     * @param $arg
     * @param $directive
     * @return array|null
     * @throws \Exception
     */
    function valuesFromArguments($info, $arg, $directive)
    {
        $argValues = null;

        if ($arg->kind === 'Argument') {
            // so far for array case, untied below
            switch ($arg->value->kind) {

                case 'IntValue':
                    // we want always to work with arrays
                    $argValues = [$arg->value->value];
                    break;

                case 'StringValue':
                    $argValues = [$arg->value->value];
                    break;

                case 'ListValue':
                    $argValues = [];
                    foreach ($arg->valus->nodes as $node) {
                        $argValues[] = $node->value;
                    }
                    break;

                case 'Variable':
                    $argValues = [];
                    foreach ($info->variableValues[$arg->value->name->value] as $value) {
                        $argValues[] = $value;
                    }
                    break;

                default:
                    $msg = 'Unexpected argument value in directive: '
                        . $directive->name;
                    throw new \Exception($msg);
                    break;
            }
        } else {
            throw new \Exception ('Unknown argument kind: '
                . $arg->kind . ' for '
                . $arg->name->value . ' in '
                . $directive->name);
        }
        return $argValues;
    }

    /**
     *
     * This is how a handler looks -- make new ones for new directives
     *
     * @param $field
     * @param $argValues
     * @param $directive
     * @param $arg
     * @return mixed
     */
    public function handleAtIdNot($field, $argValues, $directive, $arg)
    {
        $listField = ''; // temporary
        $value = ''; // temporary

        switch ($arg->name->value) {

            case 'in':
                if ($inVals = $this->inValsFromValues($argValues)) {
                    // only non-empty (@idNot in:) values arrays
                    // and, this way matches formation of condition (not-@) idNot
                    $helped = Db::parseParam('elements.id', $inVals, '!=');
                    $field = $field->where
                        ? $field->andWhere($helped)
                        : $field->where($helped);
                }
                break;

            // here other arguments for a directive would be handled similarly

            default:
                throw new \Exception('Unexpected argument: ' +
                    $arg->name->value +
                    ' in directive: ' + $directive->name);
        }

        return $field;
    }

    /**
     * @param $argValues
     * @param $inVals
     * @return string
     */
    public function inValsFromValues($argValues): string
    {
        $i = 0;
        $inVals = null;

        foreach ($argValues as $value) {
            $i++ === 0
                ? $inVals = 'and, !=' . $value
                : $inVals .= ', !=' . $value;
        }

        return $inVals;
    }
}
