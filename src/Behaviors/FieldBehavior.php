<?php

namespace markhuot\CraftQL\Behaviors;

use markhuot\CraftQL\Builders\Field;
use yii\base\Behavior;

class FieldBehavior extends Behavior {

    /**
     * @var Field
     */
    public $owner;

}