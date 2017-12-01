<?php

namespace markhuot\CraftQL\Types;

// use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Request;

class EntryEdge extends ObjectType {

    protected function fields(Request $request) {
        return function () use ($request) {
            return [
                'cursor' => Type::string(),
                'node' => [
                    'type' => Entry::interface($request),
                    'resolve' => function ($root, $args, $context, $info) {
                        return $root['node'];
                    }
                ],
                'relatedTo' => (new \markhuot\CraftQL\GraphQLFields\Query\Edge\RelatedTo($request))->toArray(),
                'drafts' => [
                    'type' => EntryDraftConnection::singleton($request),
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
                ],

                // @optional could expose each entry type next to the generic node
                // foreach ($request->entryTypes()->all() as $entryType) {
                //     $fields[$entryType->config['craftType']->handle] = ['type' => $entryType, 'resolve' => function ($root, $args) {
                //         return $root['node'];
                //     }];
                // }
            ];
        };
    }

}