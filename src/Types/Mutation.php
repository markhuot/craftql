<?php

namespace markhuot\CraftQL\Types;

use yii\base\Component;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Craft;
use craft\elements\Entry;

class Mutation extends Component {

    private $sections;

    function getType() {
        $entryTypes = \markhuot\CraftQL\Types\EntryType::all();

        foreach ($entryTypes as $entryType) {
            $args = \markhuot\CraftQL\Types\Entry::baseInputArgs();
            $args = array_merge($args, $entryType->args());

            $fields['upsert'.ucfirst($entryType->name)] = [
                'type' => $entryType,
                'args' => $args,
                'resolve' => $entryType->upsert(),
            ];
        }

        return new ObjectType([
            'name' => 'Mutation',
            'fields' => $fields
        ]);
    }

}