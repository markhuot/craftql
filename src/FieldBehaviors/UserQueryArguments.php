<?php

namespace markhuot\CraftQL\FieldBehaviors;

use markhuot\CraftQL\Behaviors\FieldBehavior;
use markhuot\CraftQL\Types\RelatedToInputType;
use markhuot\CraftQL\Types\StatusEnum;

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
        $this->owner->addStringArgument('orderBy');
        $this->owner->addArgument('relatedTo')->type(RelatedToInputType::class)->lists();
        $this->owner->addStringArgument('search');
        $this->owner->addArgument('status')->type(StatusEnum::class)->lists();
        $this->owner->addStringArgument('username');

        $fieldService = \Yii::$container->get('craftQLFieldService');
        $arguments = $fieldService->getQueryArguments($this->owner->getRequest());
        $this->owner->addArguments($arguments, false);
    }

}
