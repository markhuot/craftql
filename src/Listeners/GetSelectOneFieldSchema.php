<?php

namespace markhuot\CraftQL\Listeners;

use markhuot\CraftQL\CraftQL;
use markhuot\CraftQL\Helpers\StringHelper;
use markhuot\CraftQL\Types\OptionFieldData;
use yii\Log\Logger;

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

        try {
            $values = static::valuesForField($field);
        }
        catch (\Exception $e) {
            if (CraftQL::getInstance()->getSettings()->throwSchemaBuildErrors) {
                throw $e;
            }
            else {
                \Craft::getLogger()->log('There was an issue building '.$field->handle.'. The error was: '.$e->getMessage(), Logger::LEVEL_WARNING);
                return;
            }
        }

        $graphqlField = $event->schema->addEnumField($field)
            ->values($values)
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

    static function valuesForField($craftField) {
        $values = [];

        foreach ($craftField['settings']['options'] as $option) {
            $value = StringHelper::graphQLEnumValueForString($option['value']);
            $name = $value === '' ? 'empty' : $value;

            if (!preg_match('~^[_a-zA-Z][_a-zA-Z0-9]*$~', $name)) {
                //$name = \craft\helpers\StringHelper::toPascalCase($option['label']);
                throw new \Exception('The field "'.$craftField->name.'" ('.$craftField->handle.') contains an invalid value, `'.$name.'`. Field values must start with a letter or underscore followed by any number of letters, numbers, and underscores. For the nerds, that\'s /~^[_a-zA-Z][_a-zA-Z0-9]*$~/');
            }

            $values[$name] = ['description' => $option['label'], 'value' => $value];
        }

        return $values;
    }
}
