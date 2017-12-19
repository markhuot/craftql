<?php

namespace markhuot\CraftQL\Listeners;

use markhuot\CraftQL\Events\GetFieldSchema;
use markhuot\CraftQL\Builders\Schema;

class GetTableFieldSchema
{
    /**
     * Handle the request for the schema
     *
     * @param \markhuot\CraftQL\Events\GetFieldSchema $event
     * @return void
     */
    function handle(GetFieldSchema $event) {
        $event->handled = true;

        $field = $event->sender;
        $schema = $event->schema;

        $schema->addObjectField($field)
            ->lists()
            ->config(function (Schema $object) use ($field) {
                $object->name(ucfirst($field->handle).'Table');
                foreach ($field->columns as $key => $columnConfig) {
                    switch ($columnConfig['type']) {
                        case 'number':
                            $object->addRawFloatField($columnConfig['handle'])
                                ->description($columnConfig['heading']);
                            break;
                        case 'checkbox':
                        case 'lightswitch':
                            $object->addRawBooleanField($columnConfig['handle'])
                                ->description($columnConfig['heading']);
                            break;
                        default:
                            $object->addRawStringField($columnConfig['handle'])
                                ->description($columnConfig['heading']);
                    }
                }
            });
    }
}
