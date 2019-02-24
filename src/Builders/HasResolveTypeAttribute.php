<?php

namespace markhuot\CraftQL\Builders;

trait HasResolveTypeAttribute {

    protected $resolveType;

    function resolveType(callable $resolveType): self {
        $this->resolveType = $resolveType;
        return $this;
    }

    function getResolveType() {
        return $this->resolveType;
    }

}