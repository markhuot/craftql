<?php

namespace markhuot\CraftQL\Repositories;

use Craft;

class EntryType {

    private $entryTypes = [];

    function load() {
        foreach (Craft::$app->sections->allSections as $section) {
            foreach ($section->entryTypes as $entryType) {
                $this->entryTypes[$entryType->id] = $entryType;
                if (!empty($section->uid)) {
                    $this->entryTypes[$entryType->uid] = $entryType;
                }
            }
        }
    }

    function get($id) {
        return $this->entryTypes[$id];
    }

    function all() {
        return $this->entryTypes;
    }

}