<?php

namespace markhuot\CraftQL\Events;

use yii\base\Event;
use GraphQL\Type\Definition\Type;

class GetFieldSchema extends Event {

    public $field;
    public $builder;

}