<?php

namespace markhuot\CraftQL\GraphQLFields;

trait ConnectionResolverTrait {

    function getResolve($root, $args, $context, $info) {
        $criteria = $this->getCriteria($root, $args, $context, $info);

        list($pageInfo, $categories) = \craft\helpers\Template::paginateCriteria($criteria);

        return [
            'totalCount' => $pageInfo->total,
            'pageInfo' => $pageInfo,
            'edges' => $categories,
            'criteria' => $criteria,
            'args' => $args,
        ];
    }

}