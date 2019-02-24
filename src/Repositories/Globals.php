<?php

namespace markhuot\CraftQL\Repositories;

use Craft;
use craft\records\GlobalSet;

class Globals {

    private $sets = [];

    function load() {
        $globalSets = (new \craft\db\Query())
            ->from(['{{%globalsets}}'])
            ->orderBy(['name' => SORT_ASC])
            ->all();

        // $globalSets = GlobalSet::find()->all();

        foreach ($globalSets as $globalSet) {
            $this->sets[$globalSet['id']] = $globalSet;
        }

        // foreach (Craft::$app->globals->allSets as $set) {
        //     $this->sets[$set->id] = $set;
        //     if (!empty($set->uid)) {
        //         $this->sets[$set->uid] = $set;
        //     }
        // }
    }

    function get($id) {
        return new \craft\elements\GlobalSet($this->sets[$id]);
    }

    function all() {
        return array_map(function ($set) {
            return $this->get($set['id']);
        }, $this->sets);
    }

}