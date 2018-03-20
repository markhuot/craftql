<?php

namespace markhuot\CraftQL\FieldBehaviors;

use markhuot\CraftQL\Behaviors\FieldBehavior;

class UserQueryArguments extends FieldBehavior {

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

        $fieldService = \Yii::$container->get('craftQLFieldService');
        $arguments = $fieldService->getQueryArguments($this->owner->getRequest());
        $this->owner->addArguments($arguments, false);
    }

}
