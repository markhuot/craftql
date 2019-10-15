<?php

namespace markhuot\CraftQL\Builders;

trait HasIsListAttribute {

    /**
     * The type
     *
     * @var mixed
     */
    protected $isList;

    /**
     * Set if a list
     *
     * @param string $isList
     * @return self
     */
    function lists($isList=true): self {
        $this->isList = $isList;
        return $this;
    }

    /**
     * Get the description
     *
     * @return bool
     */
    function getIsList(): bool {
        return $this->isList;
    }

}
