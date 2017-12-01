<?php

namespace markhuot\CraftQL\Types;

// use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Request;

class EntryDraftEdge extends ObjectType {

    protected function fields(Request $request) {
        return function () use ($request) {
            return [
                'cursor' => Type::string(),
                'node' => ['type' => Entry::interface($request), 'resolve' => function ($root, $args, $context, $info) {
                    return $root['node'];
                }],
                'draftInfo' => [
                    'type' => \markhuot\CraftQL\Types\EntryDraftInfo::type($request),
                    'resolve' => function ($root, $args, $context, $info) {
                        return $root['node'];
                    },
                ],
            ];
        };
    }

}