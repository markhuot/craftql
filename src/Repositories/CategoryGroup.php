<?php

namespace markhuot\CraftQL\Repositories;

use Craft;
use yii\base\Component;

class CategoryGroup extends Component {

    private $groups = [];

    function load() {
        foreach (Craft::$app->categories->allGroups as $group) {
            if (!isset($this->groups[$group->id])) {
                $this->groups[$group->id] = $group;
                if (!empty($group->uid)) {
                    $this->groups[$group->uid] = $group;
                }
            }
        }
    }

    function get($id) {
      if (empty($this->groups[$id])) {
          return false;
      }
      return $this->groups[$id];
    }

    function all() {
        return $this->groups;
    }

}
