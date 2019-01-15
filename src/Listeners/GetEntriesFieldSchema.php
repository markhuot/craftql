<?php

namespace markhuot\CraftQL\Listeners;

use markhuot\CraftQL\FieldBehaviors\EntryQueryArguments;
use markhuot\CraftQL\Types\EntryInterface;
use markhuot\CraftQL\Types\EntryConnection;

class GetEntriesFieldSchema
{
    /**
     * Handle the request for the schema
     *
     * @param \markhuot\CraftQL\Events\GetFieldSchema $event
     * @return void
     */
    function handle($event) {
        $event->handled = true;

        $request = $event->schema->request;
        $field = $event->sender;

        $event->schema->addField($field)
            ->type(EntryInterface::class)
            ->use(new EntryQueryArguments)
            ->lists()
            ->resolve(function ($root, $args, $context, $info) use ($field, $request) {
                return $request->entries($root->{$field->handle}, $root, $args, $context, $info)
                    ->all();
            });

        $event->schema->addField($field)
            ->type(EntryConnection::class)
            ->use(new EntryQueryArguments)
            ->name("{$field->handle}Connection")
            ->resolve(function ($root, $args, $context, $info) use ($field, $request) {
                $criteria = $request->entries($root->{$field->handle}, $root, $args, $context, $info);
                list($pageInfo, $entries) = \craft\helpers\Template::paginateCriteria($criteria);
                $pageInfo->limit = @$args['limit'] ?: 100;

                return [
                    'totalCount' => $pageInfo->total,
                    'pageInfo' => $pageInfo,
                    'edges' => $entries,
                    'criteria' => $criteria,
                    'args' => $args,
                ];
            });

        $event->mutation->addIntArgument($field)
            ->lists();
    }
}
