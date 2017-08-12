<?php

namespace markhuot\CraftQL\Repositories;

use Craft;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Plugin;
use yii\base\Component;

class CategoryGroup extends Component {

    private $groups = [];

    function load() {
        foreach (Craft::$app->categories->allGroups as $group) {
            if (!isset($this->groups[$group->id])) {
              $this->groups[$group->id] = $group;
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
