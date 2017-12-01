<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Request;

class TagEdge extends ObjectType {

    protected function fields(Request $request) {
        return function () use ($request) {
            return [
                'cursor' => Type::string(),
                'node' => [
                    'type' => \markhuot\CraftQL\Types\Tag::interface($request),
                    'resolve' => function ($root, $args, $context, $info) {
                        return $root['node'];
                    }
                ],
                'relatedTo' => (new \markhuot\CraftQL\GraphQLFields\Query\Edge\RelatedTo($request))->toArray(),
            ];
        };
    }

}