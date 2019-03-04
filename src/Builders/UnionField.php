<?php

namespace markhuot\CraftQL\Builders;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\UnionType;
use markhuot\CraftQL\Request;

class UnionField extends Field {

    function __construct(Request $request, string $name) {
        parent::__construct($request, $name);

        $this->type = (new Union($this->request))
            ->name(ucfirst($this->getName()).'Union');
        $this->request->registerType($this->type->getName(), $this->type);
    }

    function __call($method, $args) {
        if (method_exists($this->type, $method)) {
            return call_user_func_array([$this->type, $method], $args);
        }

        throw new \Exception('Method '.$method.' not found on '.get_class($this));
    }

}