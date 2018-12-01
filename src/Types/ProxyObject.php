<?php

namespace markhuot\CraftQL\Types;

class ProxyObject {

    protected $source;

    function __construct($source) {
        $this->source = $source;
    }

    function getSource() {
        return $this->source;
    }

    function getProxiedValue($name, $request, $source, $args, $context, $info) {
        if (isset($this->source->{$name})) {
            return $this->source->{$name};
        }

        if (method_exists($this->source, 'get'.ucfirst($name))) {
            return $this->source->{'get'.ucfirst($name)}();
        }

        if (isset($this->{$name})) {
            return $this->{$name};
        }

        if (method_exists($this, 'getCraftQL'.ucfirst($name))) {
            return $this->{'getCraftQL'.ucfirst($name)}($request, $source, $args, $context, $info);
        }

        if (method_exists($this, 'get'.ucfirst($name))) {
            return $this->{'get'.ucfirst($name)}();
        }

        return null;
    }

}