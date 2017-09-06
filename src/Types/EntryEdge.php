<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;

class EntryEdge extends ObjectType {

    static function type($request) {
        $fields = [];
        $fields['cursor'] = Type::string();
        $fields['node'] = ['type' => \markhuot\CraftQL\Types\Entry::interface($request), 'resolve' => function ($root, $args, $context, $info) {
            return $root['node'];
        }];

        // @optional could expose each entry type next to the generic node
        // foreach ($request->entryTypes()->all() as $entryType) {
        //     $fields[$entryType->config['craftType']->handle] = ['type' => $entryType, 'resolve' => function ($root, $args) {
        //         return $root['node'];
        //     }];
        // }

        return new static([
            'name' => 'EntryEdge',
            'fields' => $fields,
        ]);
    }

}