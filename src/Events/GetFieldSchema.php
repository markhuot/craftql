<?php

namespace markhuot\CraftQL\Events;

use yii\base\Event;
use markhuot\CraftQL\Builders\Schema as SchemaBuilder;
use markhuot\CraftQL\Builders\Field as FieldBuilder;
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
     * @var SchemaBuilder
     */
    public $schema;

    /**
     * The schema to build
     *
     * @var FieldBuilder
     */
    public $query;

    /**
     * The mutation arguments
     *
     * @var FieldBuilder
     */
    public $mutation;

}
