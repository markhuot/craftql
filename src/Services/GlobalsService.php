<?php

namespace markhuot\CraftQL\Services;

use Craft;
use craft\db\Query;
use markhuot\CraftQL\Models\Token;

class GlobalsService {

    private $sets = [];

    function __construct() {
        $globalSets = (new \craft\db\Query())
            ->from(['{{%globalsets}}'])
            ->orderBy(['name' => SORT_ASC])
            ->all();

        foreach ($globalSets as $globalSet) {
            $globalSet['craftQlTypeName'] = ucfirst($globalSet['handle']);
            $this->sets[$globalSet['id']] = $globalSet;
        }
    }

    /**
     * @return array
     */
    function all() {
        return $this->sets;
    }

}