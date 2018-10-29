<?php

namespace markhuot\CraftQL\Repositories;

use Craft;
use yii\base\Component;

abstract class Repository extends Component {

    private $items = null;

    /**
     * @return array
     */
    abstract function load();

    function maybeLoad() {
        if ($this->items !== null) {
            return;
        }

        $this->items = $this->load();
    }

    function get($id) {
        $this->maybeLoad();

        if (empty($this->items[$id])) {
            return false;
        }

        return $this->items[$id];
    }

    function all() {
        $this->maybeLoad();

        return $this->items;
    }

}
