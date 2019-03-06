<?php

namespace markhuot\CraftQL\Services;

use Craft;
use craft\db\Query;
use markhuot\CraftQL\Models\Token;

class CategoryGroupsService {

    private $groupsById = [];
    private $groupsByUid = [];

    function __construct() {
        $categoryGroups = (new Query())
            ->select(['id', 'uid', 'structureId', 'fieldLayoutId', 'name', 'handle'])
            ->from(['{{%categorygroups}}'])
            ->orderBy(['name' => SORT_ASC])
            ->all();

        foreach ($categoryGroups as $group) {
            $group['craftQlTypeName'] = ucfirst($group['handle']).'Category';
            $this->groupsById[$group['id']] = $group;
            $this->groupsByUid[$group['uid']] = $group;
        }
    }

    /**
     * Get the GraphQL type name of the passed identifier
     *
     * @param $idOrUid
     * @return mixed
     */
    function getTypeNameByIdOrUid($idOrUid) {
        if (is_numeric($idOrUid)) {
            return @$this->groupsById[$idOrUid]['craftQlTypeName'];
        }

        return @$this->groupsByUid[$idOrUid]['craftQlTypeName'];
    }

    /**
     * @return array
     */
    function all() {
        return array_values($this->groupsById);
    }

    /**
     * Get all the site handles
     *
     * @return array
     */
    function getAllHandles() {
        return $this->handles = array_map(function ($group) {
            return $group->handle;
        }, $this->groupsById);
    }

}