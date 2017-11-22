<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;

class EntryEdge extends Edge {

    static function baseFields($request) {
        $fields = parent::baseFields($request);

        $fields['relatedTo'] = (new \markhuot\CraftQL\GraphQLFields\Edge\RelatedTo($request))->toArray();

        $fields['drafts'] = [
            'type' => \markhuot\CraftQL\Types\EntryDraftConnection::make($request),
            'resolve' => function ($root, $args, $context, $info) use ($request) {
                $drafts = \Craft::$app->entryRevisions->getDraftsByEntryId($root['node']->id);

                return [
                    'totalCount' => count($drafts),
                    'pageInfo' => [
                        'currentPage' => 1,
                        'totalPages' => 1,
                        'first' => 1,
                        'last' => 1,
                    ],
                    'edges' => $drafts,
                ];
            },
        ];

        // @optional could expose each entry type next to the generic node
        // foreach ($request->entryTypes()->all() as $entryType) {
        //     $fields[$entryType->config['craftType']->handle] = ['type' => $entryType, 'resolve' => function ($root, $args) {
        //         return $root['node'];
        //     }];
        // }

        return $fields;
    }

}