<?php

namespace markhuot\CraftQL\Services;

use Craft;
use craft\db\Query;
use markhuot\CraftQL\Models\Token;

class CategoryGroupsService {

    private $groups = [];

    function __construct() {
        $categoryGroups = (new Query())
            ->select(['id', 'structureId', 'fieldLayoutId', 'name', 'handle'])
            ->from(['{{%categorygroups}}'])
            ->orderBy(['name' => SORT_ASC])
            ->all();

        foreach ($categoryGroups as $group) {
            $group['craftQlTypeName'] = ucfirst($group['handle']).'Category';
            $this->groups[$group['id']] = $group;
        }
    }

    /**
     * @return array
     */
    function all() {
        return $this->groups;
    }

    /**
     * Get all the site handles
     *
     * @return array
     */
    function getAllHandles() {
        return $this->handles = array_map(function ($group) {
            return $group->handle;
        }, $this->groups);
    }

}