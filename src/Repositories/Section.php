<?php

namespace markhuot\CraftQL\Repositories;

use Craft;

class Section extends Repository {

    function load() {
        $sections = [];

        foreach (Craft::$app->sections->allSections as $section) {
            $sections[$section->id] = $section;
        }

        return $sections;
    }

}