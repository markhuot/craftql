<?php

namespace markhuot\CraftQL\Services;

use craft\db\Query;

class VolumesService {

    private $volumes = [];

    function __construct() {
        $volumes = (new Query())
            ->select([
                'id',
                'dateCreated',
                'dateUpdated',
                'name',
                'handle',
                'hasUrls',
                'url',
                'sortOrder',
                'fieldLayoutId',
                'type',
                'settings',
            ])
            ->from(['{{%volumes}}'])
            ->orderBy('sortOrder asc')
            ->all();

        foreach ($volumes as $volume) {
            $volume['craftQlTypeName'] = ucfirst($volume['handle']).'Volume';
            $this->volumes[$volume['id']] = $volume;
        }
    }

    /**
     * @return array
     */
    function all() {
        return $this->volumes;
    }

    /**
     * Get all the site handles
     *
     * @return array
     */
    function getAllHandles() {
        return $this->handles = array_map(function ($volume) {
            return $volume->handle;
        }, $this->volumes);
    }

}