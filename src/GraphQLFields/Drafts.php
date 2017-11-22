<?php

namespace markhuot\CraftQL\GraphQLFields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\GraphQLFields\Base as BaseField;
use markhuot\CraftQL\Types\EntryDraft;

class Drafts extends BaseField {

    protected $description = 'A list of entries from Craft';

    function getType() {
        return Type::listOf(EntryDraft::interface($this->request));
    }

    function getArgs() {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The entry id to query for drafts'
            ],
        ];
    }

    function getResolve($root, $args, $context, $info) {
        return \Craft::$app->entryRevisions->getDraftsByEntryId($args['id']);
    }

}