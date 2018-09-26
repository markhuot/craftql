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

//        return $this->resolve;

        return function($root, $args, $context, $info) {
//            \Yii::beginProfile('getResolve', 'getResolve');

            // @TODO this is costly and takes a lot of time to complete. see if there's a better way to do this
            $event = new ResolveField;
            $event->root = $root;
            $event->args = $args;
            $event->context = $context;
            $event->info = $info;
            Event::trigger($this, 'craftqlresolve', $event);
            $args = $event->args;

            if (is_callable($this->resolve)) {
                $foo = call_user_func_array($this->resolve, [$root, $args, $context, $info]);
            }

            else if ($this->resolve !== null) {
                $foo = $this->resolve;
            }

//            \Yii::endProfile('getResolve', 'getResolve');
            return $foo;
        };
    }

}