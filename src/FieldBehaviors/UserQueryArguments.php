<?php

namespace markhuot\CraftQL\FieldBehaviors;

use yii\base\Behavior;
use GraphQL\Type\Definition\Type;

class UserQueryArguments extends Behavior {

    function initUserQueryArguments() {
        $this->owner->addBooleanArgument('admin');
        $this->owner->addStringArgument('email');
        $this->owner->addStringArgument('firstName');
        $this->owner->addStringArgument('group');
        $this->owner->addStringArgument('groupId');
        $this->owner->addIntArgument('id');
        $this->owner->addIntArgument('lastLoginDate');
        $this->owner->addStringArgument('lastName');
        $this->owner->addIntArgument('limit');
        $this->owner->addIntArgument('offset');
        $this->owner->addStringArgument('order');
        $this->owner->addStringArgument('search');
        // $this->owner->addStringArgument('status' => static::statusEnum(),
        $this->owner->addStringArgument('username');
    }

}
