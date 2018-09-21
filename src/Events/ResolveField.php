<?php

namespace markhuot\CraftQL\Events;

use yii\base\Event;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Builders\Schema as SchemaBuilder;
use markhuot\CraftQL\Builders\Field as FieldBuilder;
use craft\base\Field;

class ResolveField extends Event {

    /**
     * The field being fetched
     *
     * @var Field
     */
    public $sender;

    public $root;

    /** @var array */
    public $args;
    public $context;
    public $info;

}