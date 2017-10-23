<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;

class EntryDraftInfo extends ObjectType {

    static $type;

    static function type($request) {
        if (static::$type) {
            return static::$type;
        }

        return static::$type = new static([
            'name' => 'EntryDraftInfo',
            'fields' => [
                'draftId' => Type::int(),
                'name' => Type::string(),
                'notes' => [
                    'type' => Type::string(),
                    'resolve' => function ($root, $args) { return $root->revisionNotes; }
                ],
            ],
        ]);
    }

}