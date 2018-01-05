<?php

namespace markhuot\CraftQL\FieldBehaviors;

use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Types\EntryConnection;
use yii\base\Behavior;

class RelatedEntriesField extends Behavior {

    function initRelatedEntriesField() {
        $this->owner->addField('relatedEntries')
            ->type(EntryConnection::class)
            // ->arguments([
            //     'source' => Type::boolean(),
            //     'target' => Type::boolean(),
            //     'field' => Type::string(),
            //     'sourceLocale' => Type::string(),
            // ])
            ->resolve(function ($root, $args, $context, $info) {
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

                $criteria = $this->owner->getRequest()->entries($criteria, $root, $args, $context, $info);
                list($pageInfo, $entries) = \craft\helpers\Template::paginateCriteria($criteria);

                return [
                    'totalCount' => $pageInfo->total,
                    'pageInfo' => $pageInfo,
                    'edges' => $entries,
                ];
            });
    }

}