<?php

namespace markhuot\CraftQL\Repositories;

use Craft;
use yii\base\Component;

class TagGroup extends Repository {

    function load() {
        $groups = [];

        foreach (Craft::$app->tags->allTagGroups as $group) {
            if (!isset($this->groups[$group->id])) {
                $groups[$group->id] = $group;
            }
        }

        return $groups;
    }

}
