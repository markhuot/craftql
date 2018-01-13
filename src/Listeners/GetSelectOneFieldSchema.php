<?php

namespace markhuot\CraftQL\Listeners;

use markhuot\CraftQL\Helpers\StringHelper;
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

        $graphqlField = $event->schema->addEnumField($field)
            ->values([static::class, 'valuesForField'], $field)
            ->resolve(function ($root, $args) use ($field) {
                return StringHelper::graphQLEnumValueForString((string)$root->{$field->handle}) ?: null;
            });

        $event->query->addStringArgument($field);

        $event->mutation->addArgument($field)
            ->type($graphqlField);
    }

    static function valuesForField($graphQLField, $craftField) {
        $values = [];

        foreach ($craftField['settings']['options'] as $option) {
            $value = StringHelper::graphQLEnumValueForString($option['value']);
            $values[$value] = ['description' => $option['label']];
        }

        return $values;
    }
}
