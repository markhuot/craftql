<?php

namespace markhuot\CraftQL\Services;

use Craft;
use craft\db\Query;
use markhuot\CraftQL\Models\Token;

class TagGroupsService {

    private $groupsById = [];
    private $groupsByUid = [];

    function __construct() {
        $tagGroups = (new Query())
            ->select(['id', 'uid', 'name', 'handle', 'fieldLayoutId'])
            ->from(['{{%taggroups}}'])
            ->orderBy(['name' => SORT_ASC])
            ->all();

        foreach ($tagGroups as $tagGroup) {
            $tagGroup['craftQlTypeName'] = ucfirst($tagGroup['handle']).'Tags';
            $this->groupsById[$tagGroup['id']] = $tagGroup;
            $this->groupsByUid[$tagGroup['uid']] = $tagGroup;
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
        return $this->handles = array_map(function ($tagGroup) {
            return $tagGroup->handle;
        }, $this->groupsById);
    }

}