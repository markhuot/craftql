<?php

namespace markhuot\CraftQL\Events;

use yii\base\Event;
use GraphQL\Type\Definition\Type;
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

    /**
     * Whether the event should continue to the default field handler
     *
     * @var bool
     */
    public $preventDefault = false;

}