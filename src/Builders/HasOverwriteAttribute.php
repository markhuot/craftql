<?php

namespace markhuot\CraftQL\Builders;

use yii\base\Event;

trait HasOverwriteAttribute {

    static $booted = false;

    static protected $overwritten = [];

    /**
     * Boot the trait. We only need one listener not one on _every_ field.
     * It would be good, later, to see if there's a better way to do this.
     */
    function bootHasOverwriteAttribute() {
        if (static::$booted) {
            return;
        }

        Event::on(Field::class, 'craftqlresolve', function ($event) {
            if (empty(static::overwrites())) {
                return;
            }

            $args = $event->args;

            $overwrites = static::overwrites();
            foreach ($overwrites as $old => $new) {
                if (isset($args[$old])) {
                    $args[$new] = $args[$old];
                    unset($args[$old]);
                }
            }


            $event->args = $args;
        });

        static::$booted = true;
    }

    /**
     * Set the argument you'd like to overwrite with this argument
     *
     * @param $fieldName
     */
    function overwrite($fieldName) {
        static::$overwritten[$this->name] = $fieldName;
        return $this;
    }


    /**
     * Get the argument this argument overwrites, if any
     *
     * @return array
     */
    function overwrites() {
        return static::$overwritten;
    }

}