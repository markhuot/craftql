<?php

namespace markhuot\CraftQL\Schema;

use markhuot\CraftQL\Builders\Schema;
use GraphQL\Type\Definition\Type;

class RelatedToGlobal {

    function apply(Schema $schema) {
        $schema->addRawField('relatedTo')
            ->type(\markhuot\CraftQL\Types\EntryConnection::singleton($schema->getRequest()))
            ->arguments([
                'source' => Type::boolean(),
                'target' => Type::boolean(),
                'field' => Type::string(),
                'sourceLocale' => Type::string(),
            ])
            ->resolve(function ($root, $args, $context, $info) use ($schema) {
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

                $criteria = $schema->getRequest()->entries($criteria, $root, $args, $context, $info);
                list($pageInfo, $entries) = \craft\helpers\Template::paginateCriteria($criteria);

                return [
                    'totalCount' => $pageInfo->total,
                    'pageInfo' => $pageInfo,
                    'edges' => $entries,
                ];
            });
    }

}