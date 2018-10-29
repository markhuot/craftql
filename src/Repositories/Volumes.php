<?php

namespace markhuot\CraftQL\Repositories;

use Craft;

class Volumes extends Repository {

    function load() {
        $volumes = [];

        foreach (Craft::$app->volumes->getAllVolumes() as $volume) {
            $volumes[$volume->id] = $volume;
        }

        return $volumes;
    }

}
