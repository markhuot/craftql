<?php

namespace markhuot\CraftQL\Repositories;

use Craft;
use craft\db\Query;

class EntryType {

    private $entryTypes = [];

    function load() {
        $entryTypes = (new Query())
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
            ->all();

        foreach ($entryTypes as $entryType) {
            $this->entryTypes[$entryType['id']] = new \craft\models\EntryType($entryType);
        }
    }

    function get($id) {
        return $this->entryTypes[$id];
    }

    function all() {
        return $this->entryTypes;
    }

}