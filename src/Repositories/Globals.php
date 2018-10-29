<?php

namespace markhuot\CraftQL\Repositories;

use Craft;

class Globals extends Repository {

    function load() {
        $sets = [];

        foreach (Craft::$app->globals->allSets as $set) {
            $sets[$set->id] = $set;
        }

        return $sets;
    }

}