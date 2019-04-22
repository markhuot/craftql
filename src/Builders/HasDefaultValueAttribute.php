<?php

namespace markhuot\CraftQL\Builders;

trait HasDefaultValueAttribute {

    /**
     * The default value
     *
     * @var mixed
     */
    protected $defaultValue = '__empty__';

    /**
     * Store the default value
     *
     * @param $defaultValue
     */
    function defaultValue($defaultValue) {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    /**
     * Get the default value
     *
     * @return mixed
     */
    function getDefaultValue() {
        return $this->defaultValue;
    }

}