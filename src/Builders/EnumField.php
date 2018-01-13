<?php

namespace markhuot\CraftQL\Builders;

class EnumField extends Field {

    function boot() {
        $this->type = (new EnumObject($this->request))
            ->name(ucfirst($this->getName()).'Enum');
    }

    function __call($method, $args) {
        if (method_exists($this->type, $method)) {
            call_user_func_array([$this->type, $method], $args);
            return $this;
        }

        throw new \Exception('Method '.$method.' not found on '.get_class($this));
    }

}