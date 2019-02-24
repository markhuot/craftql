<?php

namespace markhuot\CraftQL\Repositories;

use Craft;
use markhuot\CraftQL\Types\Query;

class EntryType {

    private $entryTypes = [];

    function load() {
        $entryTypes = (new \craft\db\Query())
            ->select([
                'id',
                'sectionId',
                'fieldLayoutId',
                'name',
                'handle',
                'hasTitleField',
                'titleLabel',
                'titleFormat',
            ])
            ->from(['{{%entrytypes}}'])
            ->orderBy(['name' => SORT_ASC])
            ->all();

        foreach ($entryTypes as $entryType) {
            $this->entryTypes[$entryType['id']] = $entryType;
        }

        // foreach (Craft::$app->sections->allSections as $section) {
        //     foreach ($section->entryTypes as $entryType) {
        //         $this->entryTypes[$entryType->id] = $entryType;
        //         if (!empty($section->uid)) {
        //             $this->entryTypes[$entryType->uid] = $entryType;
        //         }
        //     }
        // }
    }

    function get($id) {
        if (empty($this->entryTypes[$id])) {
            return false;
        }

        $entryType = new \craft\models\EntryType($this->entryTypes[$id]);

        return $entryType;
    }

    function all() {
        return array_map(function ($entryType) {
            return $this->get($entryType['id']);
        }, $this->entryTypes);
    }

}