<?php

namespace markhuot\CraftQL\Listeners;

use markhuot\CraftQL\Events\GetFieldSchema;

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

        $outputSchema = $schema->createObjectType(ucfirst($field->handle).'Table');
        $inputSchema = $event->mutation->createInputObjectType(ucfirst($field->handle).'Input');
        $handleMapping = [];

        foreach ($field->columns as $key => $columnConfig) {
            $handleMapping[$columnConfig['handle']] = $key;

            switch ($columnConfig['type']) {
                case 'number':
                    $outputSchema->addFloatField($columnConfig['handle'])
                        ->description($columnConfig['heading']);
                    $inputSchema->addFloatArgument($columnConfig['handle'])
                        ->description($columnConfig['heading']);
                    break;
                case 'checkbox':
                case 'lightswitch':
                    $outputSchema->addBooleanField($columnConfig['handle'])
                        ->description($columnConfig['heading']);
                    $inputSchema->addBooleanArgument($columnConfig['handle'])
                        ->description($columnConfig['heading']);
                    break;
                default:
                    $outputSchema->addStringField($columnConfig['handle'])
                        ->description($columnConfig['heading']);
                    $inputSchema->addStringArgument($columnConfig['handle'])
                        ->description($columnConfig['heading']);
            }
        }

        $schema->addField($field)
            ->lists()
            ->type($outputSchema);

        $event->query->addStringArgument($field);

        $event->mutation->addArgument($field)
            ->lists()
            ->type($inputSchema)
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
