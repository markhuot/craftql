<?php

namespace markhuot\CraftQL\Behaviors;

use yii\base\Behavior;

class User extends Behavior {

    /**
     * @var \craft\elements\User
     */
    public $owner;

    public function getCraftQLPermissions() {
        return \Craft::$app->getUserPermissions()->getPermissionsByUserId($this->owner->id);
    }

}