<?php

namespace markhuot\CraftQL\Builders;

use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Events\ResolveField;
use yii\base\Event;

trait HasResolveAttribute {

    /**
     * The type
     *
     * @var mixed
     */
    protected $resolve;

    /**
     * The resolve function (or static value)
     *
     * @param mixed $resolve
     * @return self
     */
    function resolve($resolve): self {
        $this->resolve = $resolve;
        return $this;
    }

    /**
     * Get the resolve callback
     *
     * @return callable|null
     */
    function getResolve() /* php 7.1: ?callable*/ {
        if (empty($this->resolve)) {
            return null;
        }

        return function($root, $args, $context, $info) {
            $event = new ResolveField;
            $event->root = $root;
            $event->args = $args;
            $event->context = $context;
            $event->info = $info;
            Event::trigger(static::class, 'craftqlresolve', $event);
            $args = $event->args;

            if (is_callable($this->resolve)) {
                return call_user_func_array($this->resolve, [$root, $args, $context, $info]);
            }

            if ($this->resolve !== null) {
                return $this->resolve;
            }
        };
    }

}