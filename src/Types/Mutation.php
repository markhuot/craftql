<?php

namespace markhuot\CraftQL\Types;

use yii\base\Component;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Craft;
use craft\elements\Entry;

class Mutation extends ObjectType {

    function __construct($token) {
        $fields = [];


        $entryTypes = \markhuot\CraftQL\Types\EntryType::some($token->mutableEntryTypeIds());
        foreach ($entryTypes as $entryType) {
            $fields['upsert'.ucfirst($entryType->name)] = [
                'type' => $entryType,
                'args' => $entryType->args(),
                'resolve' => $entryType->upsert(),
            ];
        }

        parent::__construct([
            'name' => 'Mutation',
            'fields' => $fields
        ]);
    }

}