<?php

namespace markhuot\CraftQL\Repositories;

use Craft;
use yii\base\Component;

class Site extends Component {

    private $sites = [];

    function load() {
        foreach (Craft::$app->sites->getAllSites() as $site) {
            if (!isset($this->sites[$site->id])) {
                $this->sites[$site->id] = $site;
                if (!empty($site->uid)) {
                    $this->sites[$site->uid] = $site;
                }
            }
        }
    }

    function get($id) {
        return $this->sites[$id];
    }

    function all() {
        return $this->sites;
    }

}
