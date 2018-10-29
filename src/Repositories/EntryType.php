<?php

namespace markhuot\CraftQL\Repositories;

use Craft;

class EntryType extends Repository {

    function load() {
        $entryTypes = [];

        foreach (Craft::$app->sections->allSections as $section) {
            foreach ($section->entryTypes as $entryType) {
                $entryTypes[$entryType->id] = $entryType;
            }
        }

        return $entryTypes;
    }

}