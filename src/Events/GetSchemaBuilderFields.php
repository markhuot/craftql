<?php

namespace markhuot\CraftQL\Events;

use yii\base\Event;
use markhuot\CraftQL\Builders\Schema;

class GetSchemaBuilderFields extends Event {

    const EVENT = 'craftQlGetSchemaBuilderFields';

    /**
     * The schema being built
     *
     * @var Schema
     */
    public $sender;
}
