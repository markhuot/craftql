<?php

namespace markhuot\CraftQL\Events;

use markhuot\CraftQL\Builders\Schema;
use yii\base\Event;

class AlterSchemaFields extends Event {

    const EVENT = 'craftQlAlterSchemaFields';

    /**
     * The schema that is being built
     *
     * @var Schema
     */
    public $schema;

}