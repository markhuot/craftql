<?php

namespace markhuot\CraftQL\Events;

use markhuot\CraftQL\Builders\Schema;
use yii\base\Event;

class AlterQuerySchema extends Event {

    const EVENT = 'craftQlAlterQuerySchema';

    /**
     * The schema to build
     *
     * @var Schema
     */
    public $query;

}