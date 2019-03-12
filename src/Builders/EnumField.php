<?php

namespace markhuot\CraftQL\Builders;

use markhuot\CraftQL\Request;

class EnumField extends Field {

    function __construct(Request $request, string $name, string $prefix='') {
        parent::__construct($request, $name);

        $this->type = (new EnumObject($this->request))
            ->name($prefix.ucfirst($this->getName()).'Enum');
        $this->request->registerType($this->type->getName(), $this->type);
    }

    function __call($method, $args) {
        if (method_exists($this->type, $method)) {
            call_user_func_array([$this->type, $method], $args);
            return $this;
        }

        throw new \Exception('Method '.$method.' not found on '.get_class($this));
    }

}