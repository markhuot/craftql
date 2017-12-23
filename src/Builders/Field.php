<?php

namespace markhuot\CraftQL\Builders;

use craft\base\Field as CraftField;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Request;
use yii\base\Component;
use GraphQL\Type\Definition\EnumType;

class Field extends BaseBuilder {

    use HasTypeAttribute;
    use HasDescriptionAttribute;
    use HasIsListAttribute;
    use HasNonNullAttribute;

    protected $resolve;
    protected $arguments = [];

    function __construct(Request $request, string $name) {
        $this->request = $request;
        $this->name = $name;
        $this->boot();
    }

    protected function boot() {

    }

    // function use(string $behavior): self {
    //     $reflect = new \ReflectionClass($behavior);
    //     $this->attachBehavior($reflect->getShortName(), $behavior);
    //     return $this;
    // }

    // function getRequest() {
    //     return $this->request;
    // }

    // function name($name): self {
    //     $this->name = $name;
    //     return $this;
    // }

    // function getName() {
    //     return $this->name;
    // }

    // /**
    //  * Set the type
    //  *
    //  * @param mixed $type
    //  * @return self
    //  */
    // function type($type): self {
    //     $this->type = $type;
    //     return $this;
    // }

    // /**
    //  * Get the defined type
    //  *
    //  * @return mixed
    //  */
    // function getType() {
    //     return $this->type;
    // }

    // function getTypeConfig() {
    //     $type = $this->getType();

    //     if (is_string($type) && is_subclass_of($type, Schema::class)) {
    //         $rawType = (new $type($this->request))->getRawGraphQLObject();
    //     }

    //     else if (is_a($type, Schema::class) || is_subclass_of($type, Schema::class)) {
    //         $rawType = $type->getRawGraphQLObject();
    //     }

    //     else {
    //         $rawType = $type ?: Type::string();
    //     }

    //     return $rawType;
    // }

    function addArgumentsByLayoutId(int $fieldLayoutId) {
        $fieldService = \Yii::$container->get('fieldService');
        $arguments = $fieldService->getMutationArguments($fieldLayoutId, $this->request, $this);
        return $this->arguments = array_merge($this->arguments, $arguments);
    }

    function addArgument($name): Argument {
        if (is_a($name, CraftField::class)) {
            return $this->arguments[] = (new Argument($this->request, $name->handle))
                ->description($name->instructions);
        }

        return $this->arguments[] = new Argument($this->request, $name);
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

    function addEnumArgument($name): Enum {
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

    function getArgumentConfig(): array {
        $arguments = [];

        foreach ($this->arguments as $argument) {
            $arguments[$argument->getName()] = $argument->getConfig();
        }

        return $arguments;
    }

    function getConfig() {
        $type = $this->getTypeConfig();

        if ($this->isList) {
            $type = Type::listOf($type);
        }

        if ($this->isNonNull) {
            $type = Type::nonNull($type);
        }

        // init behaviors
        if ($behaviors=$this->getBehaviors()) {
            foreach ($behaviors as $key => $behavior) {
                $this->{"init{$key}"}();
            }
        }

        return [
            'type' => $type,
            'description' => $this->getDescription(),
            'args' => $this->getArgumentConfig(),
            'resolve' => $this->getResolve(),
        ];
    }

    // function description($description): self {
    //     $this->description = $description;
    //     return $this;
    // }

    // function getDescription() /* php 7.1: ?string*/ {
    //     return $this->description;
    // }

    // function lists($isList=true): self {
    //     $this->isList = $isList;
    //     return $this;
    // }

    // function nonNull(): self {
    //     $this->isNonNull = true;
    //     return $this;
    // }

    // function isNonNull(): bool {
    //     return $this->isNonNull;
    // }

    function resolve($resolve): self {
        $this->resolve = $resolve;
        return $this;
    }

    function getResolve() /* php 7.1: ?callable*/ {
        if (is_callable($this->resolve)) {
            return $this->resolve;
        }

        if ($this->resolve !== null) {
            return function($root, $args) {
                return $this->resolve;
            };
        }

        return null;
    }

}