<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;

class EntryEdge extends Edge {

    static function baseFields($request) {
        $fields = parent::baseFields($request);

        $fields['relatedTo'] = [
            'type' => \markhuot\CraftQL\Types\EntryConnection::make($request),
            'args' => array_merge(\markhuot\CraftQL\Types\Entry::args($request), [
                'source' => Type::boolean(),
                'target' => Type::boolean(),
                'field' => Type::string(),
                'sourceLocale' => Type::string(),
            ]),
            'resolve' => function ($root, $args, $context, $info) use ($request) {
                $criteria = \craft\elements\Entry::find();
                $criteria = $criteria->relatedTo([
                    'element' => !@$args['source'] && !@$args['target'] ? $root['node']->id : null,
                    'sourceElement' => @$args['source'] == true ? $root['node']->id : null,
                    'targetElement' => @$args['target'] == true ? $root['node']->id : null,
                    'field' => @$args['field'] ?: null,
                    'sourceLocale' => @$args['sourceLocale'] ?: null,
                ]);
                unset($args['source']);
                unset($args['target']);
                unset($args['field']);
                unset($args['sourceLocale']);
                $criteria = $request->entries($criteria, $args, $info);
                list($pageInfo, $entries) = \craft\helpers\Template::paginateCriteria($criteria);

                return [
                    'totalCount' => $pageInfo->total,
                    'pageInfo' => $pageInfo,
                    'edges' => $entries,
                ];
            },
        ];

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