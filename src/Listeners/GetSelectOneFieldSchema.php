<?php

namespace markhuot\CraftQL\Listeners;

class GetSelectOneFieldSchema
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
            ->values([static::class, 'valuesForField'])
            ->resolve(function ($root, $args) use ($field) {
                return (string)$root->{$field->handle} ?: null;
            });
        // $schema->addEnumArgument($field, $enum);
    }

    static function valuesForField($field) {
        $values = [];

        foreach ($field['settings']['options'] as $option) {
            $value = $option['value'];
            $value = preg_replace('/[^a-z0-9]+/i', ' ', $value);
            $value = \craft\helpers\StringHelper::toCamelCase($value);
            $values[$value] = ['description' => $option['label']];
        }

        return $values;
    }
}
