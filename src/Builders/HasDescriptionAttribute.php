<?php

namespace markhuot\CraftQL\Builders;

trait HasDescriptionAttribute {

    /**
     * The type
     *
     * @var mixed
     */
    protected $description;

    /**
     * Set the description
     *
     * @param string $description
     * @return self
     */
    function description(/*7.1: ?string */ $description): self {
        $this->description = $description;
        return $this;
    }

    /**
     * Get the description
     *
     * @return string
     */
    function getDescription() /* php 7.1: ?string*/ {
        return $this->description;
    }

}
