<?php

namespace markhuot\CraftQL\Services;

use Craft;

class SitesService {

    private $sites = [];

    function __construct() {
        foreach (Craft::$app->sites->getAllSites() as $site) {
            if (!isset($this->sites[$site->id])) {
                $this->sites[$site->id] = $site;
                if (!empty($site->uid)) {
                    $this->sites[$site->uid] = $site;
                }
            }
        }
    }

    function handles() {
        return $this->handles = array_map(function ($site) {
            return $site->handle;
        }, $this->sites);
    }

}