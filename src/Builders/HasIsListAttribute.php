<?php

namespace markhuot\CraftQL\Builders;

use GraphQL\Type\Definition\Type;

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
     * @return string
     */
    function getIsList(): boolean {
        return $this->isList;
    }

}