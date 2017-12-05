<?php

namespace markhuot\CraftQL\GraphQLFields\Query;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\GraphQLFields\Base as BaseField;
use markhuot\CraftQL\Types\Entry;

class EntriesConnection extends Entries {

    protected $description = 'A list of entries through a Connection';

    function getType() {
        return \markhuot\CraftQL\Types\EntryConnection::singleton($this->request);
    }

    function getResolve($root, $args, $context, $info) {
        $criteria = $this->getCriteria($root, $args, $context, $info);
        list($pageInfo, $entries) = \craft\helpers\Template::paginateCriteria($criteria);

        return [
            'totalCount' => $pageInfo->total,
            'pageInfo' => $pageInfo,
            'edges' => $entries,
            'criteria' => $criteria,
            'args' => $args,
        ];
    }

}