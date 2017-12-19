<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Builders\Schema;
use markhuot\CraftQL\Request;

class Category extends Schema {

    protected $interfaces = [
        CategoryInterface::class,
    ];

    function __construct(Request $request, $context=null) {
        parent::__construct($request, $context);
    }

    function boot() {
        $this->addFieldsByLayoutId($this->context->fieldLayoutId);
    }

    function getName(): string {
        return ucfirst($this->context->handle).'Category';
    }

    static function args($request) {
        return [
            'ancestorOf' => Type::int(),
            'ancestorDist' => Type::int(),
            'level' => Type::int(),
            'descendantOf' => Type::int(),
            'descendantDist' => Type::int(),
            'fixedOrder' => Type::boolean(),
            // 'group' => $this->request->categoryGroups()->enum(),
            'groupId' => Type::int(),
            'id' => Type::int(),
            'indexBy' => Type::string(),
            'limit' => Type::int(),
            'locale' => Type::string(),
            'nextSiblingOf' => Type::int(),
            'offset' => Type::int(),
            'order' => Type::string(),
            'positionedAfter' => Type::int(),
            'positionedBefore' => Type::int(),
            'prevSiblingOf' => Type::int(),
            // 'relatedTo' => Type::listOf(Entry::relatedToInputObject()),
            'search' => Type::string(),
            'siblingOf' => Type::int(),
            'slug' => Type::string(),
            'title' => Type::string(),
            'uri' => Type::string(),
        ];
    }

}