<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Request;
use markhuot\CraftQL\Types\Tag;
use markhuot\CraftQL\Builders\Schema;

class TagEdge extends ObjectType {

    protected function fields(Request $request) {
        return function () use ($request) {
            $schema = new Schema($request);
            $schema->addRawStringField('cursor');
            $schema->addRawField('node')
                ->type(Tag::interface($request))
                ->resolve(function ($root) {
                    return $root['node'];
                });
            $schema->addGlobalField('relatedTo');
            return $schema->config();
        };
    }

}