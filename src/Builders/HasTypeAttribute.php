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

        if (is_string($type) && is_subclass_of($type, Schema::class)) {
            $rawType = (new $type($this->request))->getRawGraphQLObject();
        }

        else if (is_a($type, Schema::class) || is_subclass_of($type, Schema::class)) {
            $rawType = $type->getRawGraphQLObject();
        }

        else if (is_a($type, InputSchema::class) || is_subclass_of($type, InputSchema::class)) {
            $rawType = $type->getRawGraphQLType();
        }

        else if (is_a($type, Union::class) || is_subclass_of($type, Union::class)) {
            $rawType = $type->getRawType();
        }

        else {
            $rawType = $type ?: Type::string();
        }

        return $rawType;
    }

}