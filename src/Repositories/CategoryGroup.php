<?php

namespace markhuot\CraftQL\Repositories;

use Craft;
use craft\db\Query;
use yii\base\Component;

class CategoryGroup extends Component {

    private $groups = [];

    function load() {
        $categoryGroups = (new Query())
            ->select(['id', 'structureId', 'fieldLayoutId', 'name', 'handle'])
            ->from(['{{%categorygroups}}'])
            ->orderBy(['name' => SORT_ASC])
            ->all();

        foreach ($categoryGroups as $group) {
            $this->groups[$group['id']] = $group;
        }

        // var_dump($categoryGroups);
        // die;

        // foreach (Craft::$app->categories->allGroups as $group) {
        //     if (!isset($this->groups[$group->id])) {
        //         $this->groups[$group->id] = $group;
        //         if (!empty($group->uid)) {
        //             $this->groups[$group->uid] = $group;
        //         }
        //     }
        // }
    }

    function get($id) {
        if (empty($this->groups[$id])) {
            return false;
        }

        $group = new \craft\models\CategoryGroup($this->groups[$id]);

        // if ($groupRecord->structure) {
        //     $group->maxLevels = $groupRecord->structure->maxLevels;
        // }

        return $group;
    }

    function all() {
        return array_map(function ($group) {
            return $this->get($group['id']);
        }, $this->groups);
    }

}
