<?php

namespace markhuot\CraftQL\Listeners;

use markhuot\CraftQL\Helpers\StringHelper;
use markhuot\CraftQL\Types\OptionFieldData;

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

        $event->query->addStringArgument($field)
            ->type($graphqlField->getType());

        $event->mutation->addArgument($field)
            ->type($graphqlField->getType());

        $event->schema->addField("{$field->handle}_FieldData")
            ->type(OptionFieldData::class)
            ->resolve(function ($root, $args, $context, $info) use ($field) {
                return [
                    'selected' => (array)$root->{$field->handle},
                    'options' => $root->{$field->handle}->getOptions(),
                ];
            });
    }

    static function valuesForField($graphQLField, $craftField) {
        $values = [];

        foreach ($craftField['settings']['options'] as $option) {
            $value = StringHelper::graphQLEnumValueForString($option['value']);
            $name = $value === '' ? 'empty' : $value;

            if (is_numeric($name)) {
                //$name = \craft\helpers\StringHelper::toPascalCase($option['label']);
                throw new \Exception('The `'.$craftField->handle.'` field contains numeric values which violates the GraphQL spec. Please use string based values instead.');
            }

            $values[$name] = ['description' => $option['label'], 'value' => $value];
        }

        return $values;
    }
}
