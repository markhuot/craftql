<?php

namespace markhuot\CraftQL\FieldBehaviors;

use markhuot\CraftQL\Behaviors\SchemaBehavior;
use markhuot\CraftQL\Types\EntryConnection;

class RelatedEntriesField extends SchemaBehavior {

    function initRelatedEntriesField() {
        $this->owner->addField('relatedEntries')
            ->type(EntryConnection::class)
            ->arguments(function($field) {
                $field->addBooleanArgument('source');
                $field->addBooleanArgument('target');
                $field->addStringArgument('field');
                $field->addStringArgument('sourceLocale');
            })
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