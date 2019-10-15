<?php

namespace markhuot\CraftQL\Builders;

trait HasDeprecationReasonAttribute {

    /**
     * If there is a reason this field is deprecated
     *
     * @var string
     */
    protected $deprecationReason = null;

    /**
     * Set the deprecation reason
     *
     * @param string $deprecationReason
     * @return self
     */
    function deprecationReason(string $deprecationReason): self {
        $this->deprecationReason = $deprecationReason;
        return $this;
    }

    /**
     * Get the deprecation reason
     *
     * @return string
     */
    function getDeprecationReason()/* php 7.2 for nullable return types : ?string*/ {
        return $this->deprecationReason;
    }

}
