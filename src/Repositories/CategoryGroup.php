<?php

namespace markhuot\CraftQL\Repositories;

use Craft;
use yii\base\Component;

class CategoryGroup extends Repository {

    function load() {
        $groups = [];

        foreach (Craft::$app->categories->allGroups as $group) {
            if (!isset($this->groups[$group->id])) {
              $groups[$group->id] = $group;
            }
        }

        return $groups;
    }

}
