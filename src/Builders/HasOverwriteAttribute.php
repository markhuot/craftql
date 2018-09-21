<?php

namespace markhuot\CraftQL\Builders;

use yii\base\Event;

trait HasOverwriteAttribute {

    /**
     * @var string
     */
    protected $overwrite;

    /**
     * Boot the trait
     */
    function bootHasOverwriteAttribute() {
        Event::on(Field::class, 'craftqlresolve', function ($event) {
            // get the args
            $args = $event->args;

            // see if this argument is even passed as an argument
            if (!isset($args[$this->name])) {
                return;
            }

            // grab the value
            $value = $args[$this->name];

            // if this argument should overwrite another arg check that
            if ($overwrite = $this->overwrites()) {

                // do the overwrite
                $args[$overwrite] = $value;
                unset($args[$this->name]);
            }

            // reset the args
            $event->args = $args;
        });
    }

    /**
     * Set the argument you'd like to overwrite with this argument
     *
     * @param $fieldName
     */
    function overwrite($fieldName) {
        $this->overwrite = $fieldName;
        return $this;
    }


    /**
     * Get the argument this argument overwrites, if any
     *
     * @return string
     */
    function overwrites() {
        return $this->overwrite;
    }

}