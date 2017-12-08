<?php

namespace markhuot\CraftQL\Listeners;

class GetSelectMultipleFieldSchema
{
    /**
     * Handle the request for the schema
     *
     * @param \markhuot\CraftQL\Events\GetFieldSchema $event
     * @return void
     */
    function handle($event) {
        $event->handled = true;

        $field = $event->sender;
        $schema = $event->schema;

        $schema->addEnumField($field)
            ->lists()
            ->values([GetSelectOneFieldSchema::class, 'valuesForField'])
            ->resolve(function ($root, $args) use ($field) {
                $values = [];

                foreach ($root->{$field->handle} as $option) {
                    $values[] = $option->value;
                }

                return $values;
            });
        // $schema->addEnumArgument($field, $enum);
    }
}
