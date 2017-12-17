<?php

namespace markhuot\CraftQL\Types;

// use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Request;
use markhuot\CraftQL\Builders\Schema;

class EntryEdge extends ObjectType {

    protected function fields(Request $request) {
        return function () use ($request) {
            $schema = new Schema($request);
            $schema->addRawStringField('cursor');
            $schema->addRawField('node')
                ->type(Entry::interface($request))
                ->resolve(function ($root) {
                    return $root['node'];
                });
            $schema->addGlobalField('relatedTo');
            $schema->addRawField('drafts')
                ->type(EntryDraftConnection::singleton($request))
                ->resolve(function ($root, $args, $context, $info) use ($request) {
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
                });
            return $schema->getFieldConfig();
        };
    }

}