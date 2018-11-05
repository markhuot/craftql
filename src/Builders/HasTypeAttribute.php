<?php

namespace markhuot\CraftQL\Builders;

use craft\fields\data\ColorData;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Types\Timestamp;
use markhuot\CraftQL\Types\Volume;

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

        else if (is_string($type) && is_subclass_of($type, ScalarType::class)) {
            if (!$this->request->hasType($type)) {
                $rawType = new $type;
                $this->request->addType($type, $rawType);
            }
            else {
                $rawType = $this->request->getType($type);
            }
        }

        else if (is_string($type) && class_exists($type)) {
            $rawType = (new InferredSchema($this->request))->parse($type)->getRawGraphQLObject();
        }

        else if (is_a($type, Schema::class) || is_subclass_of($type, Schema::class)) {
            $rawType = $type->getRawGraphQLObject();
        }

        else if (is_a($type, InputSchema::class) || is_subclass_of($type, InputSchema::class)) {
            $rawType = $type->getRawGraphQLType();
        }

        else if ($type === 'string') { $rawType = Type::string(); }
        else if ($type === 'int') { $rawType = Type::int(); }
        else if ($type === 'float') { $rawType = Type::float(); }
        else if ($type === 'bool') { $rawType = Type::boolean(); }

        else {
            $rawType = $type ?: Type::string();
        }

        if (is_string($rawType)) {
            throw new \Exception('Could not find type: '.$type);
        }

        return $rawType;
    }

}