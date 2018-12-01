<?php

namespace markhuot\CraftQL\Listeners;

/**
 * Schema for Dolphiq/craft3-iconpicker
 */

class GetIconPickerFieldSchema
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

        $event->schema->addField($field)
            ->arguments(function ($field) {
                $field->addEnumArgument('as')
                    ->values([
                        'hex',
                        'char',
                        'charHex',
                        'span',
                        'class'
                    ]);
          })
          ->description('Use argument [as] to change the output of the icon')
          ->resolve(function ($root, $args) use ($field) {
            if (!empty($args['as'])) {
                return (string)$root->{$field->handle}->{'getIcon' . ucfirst($args['as'])}();
            }

            return (string)$root->{$field->handle}->getChar();
          });

        $event->query->addStringArgument($event->sender);
        $event->mutation->addStringArgument($event->sender);
    }
}
