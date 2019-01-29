<?php

namespace markhuot\CraftQL\Repositories;

use Craft;

class Globals {

    private $sets = [];

    function load() {
        foreach (Craft::$app->globals->allSets as $set) {
            $this->sets[$set->id] = $set;
            if (!empty($set->uid)) {
                $this->sets[$set->uid] = $set;
            }
        }
    }

    function get($id) {
        return $this->sets[$id];
    }

    function all() {
        return $this->sets;
    }

}