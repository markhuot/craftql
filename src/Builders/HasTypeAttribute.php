<?php

namespace markhuot\CraftQL\Builders;

use GraphQL\Type\Definition\Type;

trait HasTypeAttribute {

    /**
     * The type
     *
     * @var mixed
     */
    protected $type;

    /**
     * Set the type
     *
     * @param mixed $type
     * @return self
     */
    function type($type): self {
        $this->type = $type;
        return $this;
    }

    /**
     * Get the defined type
     *
     * @return mixed
     */
    function getType() {
        return $this->type;
    }

    /**
     * Get the GraphQL compatible type
     *
     * @return Type
     */
    function getTypeConfig(): Type {
        $type = $this->getType();

        if (is_string($type)) {
            $type = new $type($this->request);
        }

        if (method_exists($type, 'getRawGraphQLObject')) {
            $rawType = $type->getRawGraphQLObject();
        }

        else {
            $rawType = $type ?: Type::string();
        }

        return $rawType;
    }

}