<?php

namespace markhuot\CraftQL\Builders;

trait HasOnSaveAttribute {

    /**
     * The save callback
     *
     * @var mixed
     */
    protected $onSave;

    /**
     * The onSave callback (or static value)
     *
     * @param mixed $onSave
     * @return self
     */
    function onSave($onSave): self {
        $this->onSave = $onSave;
        return $this;
    }

    /**
     * Get the resolve callback
     *
     * @return callable|null
     */
    function getOnSave() /* php 7.1: ?callable*/ {
        if (is_callable($this->onSave)) {
            return $this->onSave;
        }

        if ($this->onSave !== null) {
            return function($value) {
                return $this->onSave;
            };
        }

        return null;
    }

}
