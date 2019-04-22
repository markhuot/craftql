<?php

namespace markhuot\CraftQL\Builders;

use GraphQL\Type\Definition\Type;
use craft\base\Field as CraftField;
use markhuot\CraftQL\Scalars\Timestamp;

trait HasArgumentsAttribute {

    protected $arguments = [];

    function arguments(callable $closure): self {
        $closure($this);
        return $this;
    }

    function addArgumentsByLayoutId($fieldLayoutId): self {
        // some places in craft lave a null field layout, so account for that
        if (!$fieldLayoutId) {
            return $this;
        }

        $fieldService = \Yii::$container->get('craftQLFieldService');
        $arguments = $fieldService->getMutationArguments($fieldLayoutId, $this->request, $this);
        $this->arguments = array_merge($this->arguments, $arguments);
        return $this;
    }

    function addArguments(array $arguments, bool $throwsException = true): self {
        foreach ($arguments as $argument) {
            if ($throwsException && !empty($this->arguments[$argument->getName()])) {
                throw new \Exception('Argument `'.$argument->getName().'` is already in use');
            }

            if (empty($this->arguments[$argument->getName()])) {
                $this->arguments[$argument->getName()] = $argument;
            }
        }

        return $this;
    }

    function addArgument($name): Argument {
        if (is_a($name, CraftField::class)) {
            return $this->arguments[$name->handle] = (new Argument($this->request, $name->handle))
                ->description($name->instructions);
        }

        return $this->arguments[$name] = new Argument($this->request, $name);
    }

    function addStringArgument($name): Argument {
        return $this->addArgument($name)->type(Type::string());
    }

    function addIdArgument($name): Argument {
        return $this->addArgument($name)->type(Type::id());
    }

    function addIntArgument($name): Argument {
        return $this->addArgument($name)->type(Type::int());
    }

    function addFloatArgument($name): Argument {
        return $this->addArgument($name)->type(Type::float());
    }

    function addBooleanArgument($name): Argument {
        return $this->addArgument($name)->type(Type::boolean());
    }

    function addDateArgument($name): Argument {
        return $this->addArgument($name)->type($this->request->getType('Timestamp'));
    }

    function addEnumArgument($name): EnumField {
        if (is_a($name, CraftField::class)) {
            return $this->arguments[] = (new EnumField($this->request, $name->handle))
                ->description($name->instructions);
        }

        return $this->arguments[] = (new EnumField($this->request, $name));
    }

    function getArguments(): array {
        return $this->arguments;
    }

    function getArgument(string $name)/* @TODO PHP 7.1 nullable return types :?Argument*/ {
        foreach ($this->arguments as $argument) {
            if ($argument->getName() == $name) {
                return $argument;
            }
        }

        return null;
    }

    /**
     * Get the arguments as a GraphQL config array
     *
     * @return array
     */
    function getArgumentConfig(): array {
        $arguments = [];

        foreach ($this->arguments as $argument) {
            $arguments[$argument->getName()] = $argument->getConfig();
        }

        return $arguments;
    }

    /**
     * Get the arguments configured for a directive
     *
     * @return array
     */
    function getDirectiveArgumentConfig(): array {
        $arguments = [];

        foreach ($this->arguments as $argument) {
            $arguments[$argument->getName()] = $argument->getDirectiveConfig();
        }

        return $arguments;
    }

}