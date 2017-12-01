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
                'args' => $entryType->getGraphQLMutationArgs($request),
                'resolve' => $entryType->upsert($request),
            ];
        }

        // $fields['upsertField'] = [
        //     'type' => \markhuot\CraftQL\Types\Entry::interface($request),
        //     'args' => [
        //         'id' => Type::nonNull(Type::int()),
        //         'json' => Type::nonNull(Type::string()),
        //     ],
        //     'resolve' => function ($root, $args) {
        //         $entry = \craft\elements\Entry::find();
        //         $entry->id($args['id']);
        //         $entry = $entry->first();

        //         $json = json_decode($args['json'], true);
        //         $fieldData = [];
        //         foreach ($json as $fieldName => $value) {
        //             if (in_array($fieldName, ['title'])) {
        //                 $entry->{$fieldName} = $value;
        //             }
        //             else {
        //                 $fieldData[$fieldName] = $value;
        //             }
        //         }

        //         if (!empty($fieldData)) {
        //             $entry->setFieldValues($fieldData);
        //         }

        //         Craft::$app->elements->saveElement($entry);

        //         return $entry;
        //     },
        // ];

        parent::__construct([
            'name' => 'Mutation',
            'fields' => $fields
        ]);
    }

}