<?php

namespace markhuot\CraftQL\Services;

use Craft;
use craft\db\Query;
use markhuot\CraftQL\Models\Token;

class TagGroupsService {

    private $tagGroups = [];

    function __construct() {
        $tagGroups = (new Query())
            ->select(['id', 'name', 'handle', 'fieldLayoutId'])
            ->from(['{{%taggroups}}'])
            ->orderBy(['name' => SORT_ASC])
            ->all();

        foreach ($tagGroups as $tagGroup) {
            $tagGroup['craftQlTypeName'] = ucfirst($tagGroup['handle']).'Tags';
            $this->tagGroups[$tagGroup['id']] = $tagGroup;
        }
    }

    /**
     * @return array
     */
    function all() {
        return $this->tagGroups;
    }

    /**
     * Get all the site handles
     *
     * @return array
     */
    function getAllHandles() {
        return $this->handles = array_map(function ($tagGroup) {
            return $tagGroup->handle;
        }, $this->tagGroups);
    }

}