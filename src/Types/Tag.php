<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Builders\Schema;

class Tag extends Schema {

    protected $interfaces = [
        TagInterface::class,
    ];

    function boot() {
        $this->addFieldsByLayoutId($this->context->fieldLayoutId);
    }

    function getName(): string {
        return ucfirst($this->context->handle).'Tags';
    }

    static function args($request) {
        return [
            'fixedOrder' => Type::boolean(),
            'group' => $request->tagGroups()->enum(),
            'groupId' => Type::int(),
            'id' => Type::int(),
            'indexBy' => Type::string(),
            'limit' => Type::int(),
            'locale' => Type::string(),
            'offset' => Type::int(),
            'order' => Type::string(),
            // 'relatedTo' => Type::listOf(Entry::relatedToInputObject()),
            'search' => Type::string(),
            'slug' => Type::string(),
            'title' => Type::string(),
        ];
    }

}