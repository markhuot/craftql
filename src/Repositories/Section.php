<?php

namespace markhuot\CraftQL\Repositories;

use Craft;

class Section {

    private $sections = [];

    function load() {
        foreach (Craft::$app->sections->allSections as $section) {
            $this->sections[$section->id] = $section;
            $this->sections[$section->uid] = $section;
        }
    }
    
    function get($id) {
        return $this->sections[$id];
    }

    function all() {
        return $this->sections;
    }

}