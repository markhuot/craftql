<?php

namespace markhuot\CraftQL\Types;

// use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Request;
use markhuot\CraftQL\Builders\Schema;

class CategoryEdge extends ObjectType {

    protected function fields(Request $request) {
        return function () use ($request) {
            $schema = new Schema($request);
            $schema->addRawStringField('cursor');
            $schema->addRawField('node')
                ->type(Category::interface($request))
                ->resolve(function ($root) {
                    return $root['node'];
                });
            $schema->addGlobalField('relatedTo');
            return $schema->config();
        };
    }

}