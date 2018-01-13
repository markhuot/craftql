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
    use HasResolveAttribute;
    use HasArgumentsAttribute;

//    protected $arguments = [];

    function __construct(Request $request, string $name) {
        $this->request = $request;
        $this->name = $name;
        $this->boot();
    }

    protected function boot() {

    }

//    function addArgumentsByLayoutId(int $fieldLayoutId): self {
//        $fieldService = \Yii::$container->get('craftQLFieldService');
//        $arguments = $fieldService->getMutationArguments($fieldLayoutId, $this->request, $this);
//        $this->arguments = array_merge($this->arguments, $arguments);
//        return $this;
//    }
//
//    function addArguments(array $arguments, bool $throwsException = true): self {
//        foreach ($arguments as $argument) {
//            if ($throwsException && !empty($this->arguments[$argument->getName()])) {
//                throw new \Exception('Argument `'.$argument->getName().'` is already in use');
//            }
//
//            if (empty($this->arguments[$argument->getName()])) {
//                $this->arguments[$argument->getName()] = $argument;
//            }
//        }
//
//        return $this;
//    }
//
//    function addArgument($name): Argument {
//        if (is_a($name, CraftField::class)) {
//            return $this->arguments[$name->handle] = (new Argument($this->request, $name->handle))
//                ->description($name->instructions);
//        }
//
//        return $this->arguments[$name] = new Argument($this->request, $name);
//    }
//
//    function addStringArgument($name): Argument {
//        return $this->addArgument($name)->type(Type::string());
//    }
//
//    function addIdArgument($name): Argument {
//        return $this->addArgument($name)->type(Type::id());
//    }
//
//    function addIntArgument($name): Argument {
//        return $this->addArgument($name)->type(Type::int());
//    }
//
//    function addFloatArgument($name): Argument {
//        return $this->addArgument($name)->type(Type::float());
//    }
//
//    function addBooleanArgument($name): Argument {
//        return $this->addArgument($name)->type(Type::boolean());
//    }

    /**
     * Create a new builder
     *
     * @param [type] $name
     * @return self
     */
    function createInputObjectType($name): InputSchema {
        return new InputSchema($this->request, $name);
    }

    // function addObjectArgument($name): Argument {
    //     return $this->addArgument($name);
    // }

//    function addEnumArgument($name): Enum {
//        if (is_a($name, CraftField::class)) {
//            return $this->arguments[] = (new EnumField($this->request, $name->handle))
//                ->description($name->instructions);
//        }
//
//        return $this->arguments[] = (new EnumField($this->request, $name));
//    }

//    function getArguments(): array {
//        return $this->arguments;
//    }
//
//    function getArgument(string $name)/* @TODO PHP 7.1 nullable return types :?Argument*/ {
//        foreach ($this->arguments as $argument) {
//            if ($argument->getName() == $name) {
//                return $argument;
//            }
//        }
//
//        return null;
//    }

//    function getArgumentConfig(): array {
//        $arguments = [];
//
//        foreach ($this->arguments as $argument) {
//            $arguments[$argument->getName()] = $argument->getConfig();
//        }
//
//        return $arguments;
//    }

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

    function onSave(callable $callback) {
        $this->onSave = $callback;
    }

    function getOnSave() {
        return $this->onSave;
    }

}