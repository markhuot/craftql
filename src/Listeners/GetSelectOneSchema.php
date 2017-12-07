<?php

namespace markhuot\CraftQL\Listeners;

class GetSelectOneSchema
{
    /**
     * Handle the request for the schema
     *
     * @param \markhuot\CraftQL\Events\GetFieldSchema $event
     * @return void
     */
    static function handle($event) {
        $event->handled = true;

        $field = $event->sender;
        $builder = $event->builder;

        $enum = $builder->newEnum(ucfirst($field->handle.'Enum'));

        if (empty($enum->getValues())) {
            foreach ($field['settings']['options'] as $option) {
                $value = $option['value'];
                $value = preg_replace('/[^a-z0-9]+/i', ' ', $value);
                $value = \craft\helpers\StringHelper::toCamelCase($value);
                $enum->addValue($value, ['description' => $option['label']]);
            }
        }

        return $builder
            ->addEnumField($field, $enum, function ($root, $args) use ($field) {
                return (string)$root->{$field->handle} ?: null;
            })
            ->addEnumArgument($field, $enum);
    }
}
