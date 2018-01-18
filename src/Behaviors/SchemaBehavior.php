<?php

namespace markhuot\CraftQL\Behaviors;

use markhuot\CraftQL\Builders\Schema;
use yii\base\Behavior;

class SchemaBehavior extends Behavior {

    /**
     * @var Schema
     */
    public $owner;

}