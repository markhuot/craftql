<?php

namespace markhuot\CraftQL\GraphQLFields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Types\Entry as EntryType;

class Entry extends Entries {

    protected $description = 'A list of entries from Craft';

    function getType() {
        return EntryType::interface($this->request);
    }

    function getResolve($root, $args, $context, $info) {
        return $this->request->entries(\craft\elements\Entry::find(), $args, $info)->one();
    }

}