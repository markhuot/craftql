<?php

namespace markhuot\CraftQL\Types;

use yii\base\Component;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Craft;
use craft\elements\Entry;

class Mutation extends ObjectType {

    function __construct($request) {
        $fields = [];

        $entryTypes = $request->entryTypes()->all('mutate');
        foreach ($entryTypes as $entryType) {
            $fields['upsert'.ucfirst($entryType->name)] = [
                'type' => $entryType,
                'args' => $entryType->args($request),
                'resolve' => $entryType->upsert($request),
            ];
        }

        parent::__construct([
            'name' => 'Mutation',
            'fields' => $fields
        ]);
    }

}