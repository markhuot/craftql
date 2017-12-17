<?php

namespace markhuot\CraftQL\Builders;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ObjectType;
use markhuot\CraftQL\Builders\ContentField;
use GraphQL\Type\Definition\UnionType;

class Union extends ContentField {

    protected $types = [];

    function resolveType(callable $resolveType): self {
        $this->resolveType = $resolveType;
        return $this;
    }

    function getResolveType() {
        return $this->resolveType;
    }

    function addType($typeName) {
        return $this->types[$typeName] = new Schema($this->request);
    }

    function getTypes(): array {
        $types = [];

        foreach ($this->types as $typeName => $typeSchema) {
            $types[] = new ObjectType([
                'name' => $typeName,
                'fields' => $typeSchema->config(),
            ]);
        }

        return $types;
    }

    function getConfig() {
        $type = new UnionType([
            'name' => ucfirst($this->field->handle).'Matrix',
            'description' => 'A union of possible blocks for this matrix field',
            'types' => $this->getTypes(),
            'resolveType' => $this->getResolveType(),
        ]);

        if ($this->isList) {
            $type = Type::listOf($type);
        }

        if ($this->isNonNull) {
            $type = Type::nonNull($type);
        }

        return [
            'type' => $type,
            'description' => $this->getDescription(),
        ];
    }

}