<?php

namespace markhuot\CraftQL\Builders;

trait HasResolveAttribute {

    /**
     * The type
     *
     * @var mixed
     */
    protected $resolve;

    /**
     * The resolve function (or static value)
     *
     * @param mixed $resolve
     * @return self
     */
    function resolve($resolve): self {
        $this->resolve = $resolve;
        return $this;
    }

    /**
     * Get the resolve callback
     *
     * @return callable|null
     */
    function getResolve() /* php 7.1: ?callable*/ {
        if (is_callable($this->resolve)) {
            return $this->resolve;
        }

        if ($this->resolve !== null) {
            return function($value) {
                return $this->resolve;
            };
        }

        return null;
    }

}
