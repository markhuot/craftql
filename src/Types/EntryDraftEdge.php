<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;

class EntryDraftEdge extends Edge {

    static function baseFields($request) {
        $fields = parent::baseFields($request);

        $fields['draftInfo'] = [
            'type' => \markhuot\CraftQL\Types\EntryDraftInfo::type($request),
            'resolve' => function ($root, $args, $context, $info) {
                return $root['node'];
            },
        ];

        return $fields;
    }

}