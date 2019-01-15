<?php

namespace markhuot\CraftQL\Builders;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\UnionType;

class Union extends Field {

    protected $types = [];
    protected $resolveType;
    protected static $rawTypes = [];

    function resolveType(callable $resolveType): self {
        $this->resolveType = $resolveType;
        return $this;
    }

    function getResolveType() {
        return $this->resolveType;
    }

    /**
     * @param $typeName
     * @param null $context
     * @return Schema
     */
    function addType($typeName, $context=null) {
        $this->types[$typeName] = new Schema($this->request, $context);
        $this->types[$typeName]->name($typeName);
        return $this->types[$typeName];
    }

    function getTypes(): array {
        return $this->types;
    }

    function getRawTypes(): array {
        $types = [];

        foreach ($this->types as $typeName => $typeSchema) {
            $types[] = new ObjectType([
                'name' => $typeName,
                'fields' => $typeSchema->getFieldConfig(),
            ]);
        }

        return $types;
    }

    function getRawType() {
        if (!empty(static::$rawTypes[$this->getName()])) {
            return static::$rawTypes[$this->getName()];
        }

        return static::$rawTypes[$this->getName()] = new UnionType([
            'name' => ucfirst($this->getName()).'Union',
            'description' => 'A union of possible blocks types',
            'types' => function () {
                return $this->getRawTypes();
            },
            'resolveType' => $this->getResolveType(),
        ]);
    }

    function getConfig() {
        $type = $this->getRawType();

        if ($this->isList) {
            $type = Type::listOf($type);
        }

        if ($this->isNonNull) {
            $type = Type::nonNull($type);
        }

        return [
            'type' => $type,
            'description' => $this->getDescription(),
            'args' => $this->getArgumentConfig(),
            'resolve' => $this->getResolve(),
        ];
    }

}