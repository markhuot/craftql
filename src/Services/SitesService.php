<?php

namespace markhuot\CraftQL\Services;

use Craft;
use craft\db\Query;
use markhuot\CraftQL\Models\Token;

class SitesService {

    private $sites = [];

    function __construct() {
        $results = (new Query())
            ->select([
                's.id',
                's.groupId',
                's.name',
                's.handle',
                'language',
                's.primary',
                's.hasUrls',
                's.baseUrl',
                's.sortOrder',
            ])
            ->from(['{{%sites}} s'])
            ->innerJoin('{{%sitegroups}} sg', '[[sg.id]] = [[s.groupId]]')
            ->orderBy(['sg.name' => SORT_ASC, 's.sortOrder' => SORT_ASC])
            ->all();

        foreach (Craft::$app->sites->getAllSites() as $site) {
            if (!isset($this->sites[$site['id']])) {
                $this->sites[$site['id']] = $site;
                // if (!empty($site->uid)) {
                //     $this->sites[$site->uid] = $site;
                // }
            }
        }
    }

    /**
     * @return array
     */
    function all() {
        return $this->sites;
    }

    /**
     * Get all the site handles
     *
     * @return array
     */
    function getAllHandles() {
        return $this->handles = array_map(function ($site) {
            return $site->handle;
        }, $this->sites);
    }

}