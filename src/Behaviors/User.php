<?php

namespace markhuot\CraftQL\Behaviors;

use yii\base\Behavior;

class User extends Behavior {

    public function getCraftQLPermissions() {
        /** @var \craft\elements\User $user */
        $user = $this->owner;
        return \Craft::$app->getUserPermissions()->getPermissionsByUserId($user->id);
    }

}