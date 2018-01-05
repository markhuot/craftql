<?php

namespace markhuot\CraftQL\Listeners;

use markhuot\CraftQL\Events\GetFieldSchema;

class GetPositionSelectFieldSchema
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

        $values = [];
        if (in_array('left', $field->options)) { $values['left'] = 'Left'; }
        if (in_array('center', $field->options)) { $values['center'] = 'Center'; }
        if (in_array('right', $field->options)) { $values['right'] = 'Right'; }
        if (in_array('full', $field->options)) { $values['full'] = 'Full'; }
        if (in_array('drop-left', $field->options)) { $values['dropLeft'] = 'Drop Left'; }
        if (in_array('drop-right', $field->options)) { $values['dropRight'] = 'Drop Right'; }

        $schema->addEnumField($field)
            ->values($values);
    }
}
