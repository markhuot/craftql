<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;

class EntryDraftEdge extends ObjectType {

    static function type($request) {
        return new static([
            'name' => 'EntryDraftEdge',

            // this has to be a callback because `EntryEdge` returns an `EntryConnection`
            // which references our own `EntryEdge` that creates an immediate circular
            // reference. The `fields` callback allows `graphql-php` to work around the
            // circular reference.
            'fields' => function () use ($request) {
                $fields = [];
                $fields['cursor'] = Type::string();
                $fields['node'] = ['type' => \markhuot\CraftQL\Types\Entry::interface($request), 'resolve' => function ($root, $args, $context, $info) {
                    return $root['node'];
                }];
                $fields['draftInfo'] = [
                    'type' => \markhuot\CraftQL\Types\EntryDraftInfo::type($request),
                    'resolve' => function ($root, $args, $context, $info) {
                        return $root['node'];
                    },
                ];
                return $fields;
            },
        ]);
    }

}