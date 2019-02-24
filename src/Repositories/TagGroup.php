<?php

namespace markhuot\CraftQL\Repositories;

use Craft;
use craft\db\Query;
use yii\base\Component;

class TagGroup extends Component {

    private $groups = [];

    function load() {
        $tagGroups = (new Query())
            ->select(['id', 'name', 'handle', 'fieldLayoutId'])
            ->from(['{{%taggroups}}'])
            ->orderBy(['name' => SORT_ASC])
            ->all();

        // var_dump($tagGroups);
        // die;

        foreach ($tagGroups as $tagGroup) {
            $this->groups[$tagGroup['id']] = $tagGroup;
        }

        // foreach (Craft::$app->tags->allTagGroups as $group) {
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

        $group = new \craft\models\TagGroup($this->groups[$id]);

        return $group;
    }

    function all() {
        return array_map(function ($group) {
            return $this->get($group['id']);
        }, $this->groups);
    }

}
