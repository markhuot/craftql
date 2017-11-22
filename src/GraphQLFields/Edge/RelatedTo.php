<?php

namespace markhuot\CraftQL\GraphQLFields\Edge;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\GraphQLFields\Entries as BaseField;
use markhuot\CraftQL\Types\Category;
use markhuot\CraftQL\Types\Entry;

class RelatedTo extends BaseField {

    protected $description = 'Entries related to the edge node';

    function getType() {
        return \markhuot\CraftQL\Types\EntryConnection::make($this->request);
    }

    function getArgs() {
        return array_merge(
            parent::getArgs(),
            [
                'source' => Type::boolean(),
                'target' => Type::boolean(),
                'field' => Type::string(),
                'sourceLocale' => Type::string(),
            ]
        );
    }

    function getResolve($root, $args, $context, $info) {
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

        $criteria = $this->request->entries($criteria, $args, $info);
        list($pageInfo, $entries) = \craft\helpers\Template::paginateCriteria($criteria);

        return [
            'totalCount' => $pageInfo->total,
            'pageInfo' => $pageInfo,
            'edges' => $entries,
        ];
    }

}