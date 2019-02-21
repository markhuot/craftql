<?php

namespace markhuot\CraftQL\Listeners;

use markhuot\CraftQL\CraftQL;
use markhuot\CraftQL\Helpers\StringHelper;
use markhuot\CraftQL\Types\MultiOptionFieldData;
use markhuot\CraftQL\Types\OptionFieldData;
use yii\Log\Logger;

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

        $craftField = $event->sender;

        try {
            $values = GetSelectOneFieldSchema::valuesForField($craftField);
        }
        catch (\Exception $e) {
            if (CraftQL::getInstance()->getSettings()->throwSchemaBuildErrors) {
                throw $e;
            }
            else {
                \Craft::getLogger()->log('There was an issue building '.$craftField->handle.'. The error was: '.$e->getMessage(), Logger::LEVEL_WARNING);
                return;
            }
        }

        $graphqlField = $event->schema->addEnumField($craftField)
            ->lists()
            ->values($values)
            ->resolve(function ($root, $args) use ($craftField) {
                $values = [];

                foreach ($root->{$craftField->handle} as $option) {
                    $values[] = StringHelper::graphQLEnumValueForString($option->value);
                }

                return $values;
            });

        // @TODO this search isn't working correctly
        $event->query->addStringArgument($craftField)
            ->type($graphqlField->getType());

        $event->mutation->addArgument($craftField)
            ->lists()
            ->type($graphqlField->getType());

        $event->schema->addField("{$craftField->handle}_FieldData")
            ->type(MultiOptionFieldData::class)
            ->resolve(function ($root, $args, $context, $info) use ($craftField) {
                return [
                    'selected' => array_map(function ($option) {
                        return [
                            'label' => $option->label,
                            'value' => $option->value,
                        ];
                    }, (array)$root->{$craftField->handle}),
                    'options' => $root->{$craftField->handle}->getOptions(),
                ];
            });
    }
}
