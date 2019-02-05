<?php

namespace markhuot\CraftQL\Builders;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\UnionType;

class Union extends BaseBuilder {

    use HasResolveAttribute;
    use HasResolveTypeAttribute;

    protected $types = [];
    protected static $rawTypes = [];

    function __construct($request, $context=null, $parent=null) {
        $this->request = $request;
        // $this->context = $context;
        // $this->parent = $parent;
    }

    /**
     * @param $typeName
     * @param null $context
     * @return Schema
     */
    function addType($typeName, $context=null) {
        $this->types[$typeName] = new Schema($this->request, $context);
        $this->types[$typeName]->name($typeName);
        $this->request->registerType($typeName, $this->types[$typeName]);
        return $this->types[$typeName];
    }

    function getTypes(): array {
        return $this->types;
    }

    function getRawTypes(): array {
        $types = [];

        foreach ($this->types as $typeName => $typeSchema) {
            $types[] = $this->request->getType($typeName);
        }

        return $types;
    }

    function getRawGraphQLObject() {
        if (!empty(static::$rawTypes[$this->getName()])) {
            return static::$rawTypes[$this->getName()];
        }

        return static::$rawTypes[$this->getName()] = new UnionType([
            'name' => $this->getName(),
            'description' => 'A union of possible blocks types',
            'types' => function () {
                return $this->getRawTypes();
            },
            'resolveType' => $this->getResolveType(),
        ]);
    }

}