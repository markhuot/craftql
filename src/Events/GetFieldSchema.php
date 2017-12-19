<?php

namespace markhuot\CraftQL\Events;

use yii\base\Event;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Builders\Schema;
use craft\base\Field;

class GetFieldSchema extends Event {

    /**
     * The field being fetched
     *
     * @var Field
     */
    public $sender;

    /**
     * The schema to build
     *
     * @var Schema
     */
    public $schema;

}