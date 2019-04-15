<?php

namespace markhuot\CraftQL\Listeners;

use markhuot\CraftQL\FieldBehaviors\EntryQueryArguments;
use markhuot\CraftQL\TypeModels\PageInfo;
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
                $totalCount = $criteria->count();
                $offset = @$args['offset'] ?: 0;
                $perPage = @$args['limit'] ?: 100;

                return [
                    'totalCount' => $totalCount,
                    'pageInfo' => new PageInfo($offset, $perPage, $totalCount),
                    'edges' => $criteria->all(),
                    'criteria' => $criteria,
                    'args' => $args,
                ];
            });

        $event->mutation->addIntArgument($field)
            ->lists();
    }
}
