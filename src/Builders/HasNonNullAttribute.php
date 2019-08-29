<?php

namespace markhuot\CraftQL\Builders;

trait HasNonNullAttribute {

    /**
     * If the schema is required
     *
     * @var boolean
     */
    protected $isNonNull = false;

    /**
     * Set if required
     *
     * @param boolean $nonnull
     * @return self
     */
    function nonNull(bool $nonNull=true): self {
        $this->isNonNull = $nonNull;
        return $this;
    }

    /**
     * Get if required
     *
     * @return boolean
     */
    function isNonNull(): bool {
        return $this->isNonNull;
    }

}
