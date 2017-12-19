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

        $tableSchema = $schema->createObjectType(ucfirst($field->handle).'Table');

        foreach ($field->columns as $key => $columnConfig) {
            switch ($columnConfig['type']) {
                case 'number':
                    $tableSchema->addFloatField($columnConfig['handle'])
                        ->description($columnConfig['heading']);
                    break;
                case 'checkbox':
                case 'lightswitch':
                    $tableSchema->addBooleanField($columnConfig['handle'])
                        ->description($columnConfig['heading']);
                    break;
                default:
                    $tableSchema->addStringField($columnConfig['handle'])
                        ->description($columnConfig['heading']);
            }
        }

        $schema->addObjectField($field)
            ->lists()
            ->config($tableSchema);
    }
}
