<?php

namespace markhuot\CraftQL\Repositories;

use Craft;
use yii\base\Component;

class TagGroup extends Component {

    private $groups = [];

    function load() {
        foreach (Craft::$app->tags->allTagGroups as $group) {
            if (!isset($this->groups[$group->id])) {
                $this->groups[$group->id] = $group;
                if (!empty($group->uid)) {
                    $this->groups[$group->uid] = $group;
                }
            }
        }
    }

    function get($id) {
      return $this->groups[$id];
    }

    function all() {
        return $this->groups;
    }

}
