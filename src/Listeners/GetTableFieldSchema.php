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
        $query = $event->query;

        $outputSchema = $query->createObjectType(ucfirst($field->handle).'Table');
        $inputSchema = $query->createInputObjectType(ucfirst($field->handle).'Input');
        $handleMapping = [];

        foreach ($field->columns as $key => $columnConfig) {
            $handleMapping[$columnConfig['handle']] = $key;

            switch ($columnConfig['type']) {
                case 'number':
                    $outputSchema->addFloatField($columnConfig['handle'])
                        ->description($columnConfig['heading']);
                    $inputSchema->addFloatField($columnConfig['handle'])
                        ->description($columnConfig['heading']);
                    break;
                case 'checkbox':
                case 'lightswitch':
                    $outputSchema->addBooleanField($columnConfig['handle'])
                        ->description($columnConfig['heading']);
                    $inputSchema->addBooleanField($columnConfig['handle'])
                        ->description($columnConfig['heading']);
                    break;
                default:
                    $outputSchema->addStringField($columnConfig['handle'])
                        ->description($columnConfig['heading']);
                    $inputSchema->addStringField($columnConfig['handle'])
                        ->description($columnConfig['heading']);
            }
        }

        $query->addObjectField($field)
            ->lists()
            ->config($outputSchema);

        $event->mutation->addArgument($field)
            ->lists()
            ->type($inputSchema->getRawGraphQLObject(true))
            ->onSave(function($value) use ($handleMapping) {
                $newValue = [];

                foreach ($value as $index => $row) {
                    foreach ($row as $oldKey => $value) {
                        $newValue[$index][$handleMapping[$oldKey]] = $value;
                    }
                }

                return $newValue;
            });
    }
}
