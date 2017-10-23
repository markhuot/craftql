<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;

class EntryDraft extends Entry {

    static function baseFields($request) {
        $baseFields = parent::baseFields($request);
        $baseFields['draftId'] = Type::int();
        $baseFields['name'] = Type::string();
        $baseFields['notes'] = ['type' => Type::string(), 'resolve' => function ($root, $args) {
            return $root->revisionNotes;
        }];

        return $baseFields;
    }

    static function resolveType($entry) {
        return \markhuot\CraftQL\Types\EntryType::getName($entry->type).'Draft';
    }

}