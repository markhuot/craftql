<?php

namespace markhuot\CraftQL\FieldBehaviors;

use markhuot\CraftQL\Behaviors\FieldBehavior;
use markhuot\CraftQL\Builders\InputSchema;

class EntryQueryArguments extends FieldBehavior {

    static $inputObjectType;

    function initEntryQueryArguments() {
        $this->owner->addStringArgument('after');
        $this->owner->addIntArgument('ancestorOf');
        $this->owner->addIntArgument('ancestorDist');
        $this->owner->addBooleanArgument('archived');
        $this->owner->addStringArgument('authorGroup');
        $this->owner->addIntArgument('authorGroupId');
        $this->owner->addIntArgument('authorId')->lists();
        $this->owner->addStringArgument('before');
        $this->owner->addIntArgument('level');
        $this->owner->addBooleanArgument('localeEnabled');
        $this->owner->addIntArgument('descendantOf');
        $this->owner->addIntArgument('descendantDist');
        $this->owner->addBooleanArgument('fixedOrder');
        $this->owner->addIntArgument('id')->lists();
        $this->owner->addIntArgument('idNot')->lists();
        $this->owner->addIntArgument('limit');
        $this->owner->addStringArgument('site');
        $this->owner->addIntArgument('siteId');
        $this->owner->addIntArgument('nextSiblingOf');
        $this->owner->addIntArgument('offset');
        $this->owner->addStringArgument('order');
        $this->owner->addStringArgument('orderBy');
        $this->owner->addIntArgument('positionedAfter');
        $this->owner->addIntArgument('positionedBefore');
        $this->owner->addStringArgument('postDate');
        $this->owner->addStringArgument('dateCreated');
        $this->owner->addStringArgument('dateUpdated');
        $this->owner->addIntArgument('prevSiblingOf');
        $this->owner->addStringArgument('relatedTo')->lists()->type($this->relatedToInputObject());
        $this->owner->addStringArgument('orRelatedTo')->lists()->type($this->relatedToInputObject());
        $this->owner->addStringArgument('search');
        $this->owner->addStringArgument('section')->lists()->type($this->owner->getRequest()->sections()->enum());
        $this->owner->addIntArgument('siblingOf');
        $this->owner->addStringArgument('slug');
        $this->owner->addStringArgument('status');
        $this->owner->addStringArgument('title');
        $this->owner->addStringArgument('type')->lists()->type($this->owner->getRequest()->entryTypes()->enum());
        $this->owner->addStringArgument('uri');

        $fieldService = \Yii::$container->get('craftQLFieldService');
        $arguments = $fieldService->getQueryArguments($this->owner->getRequest());
        $this->owner->addArguments($arguments, false);
    }

    function relatedToInputObject() {
        if (!empty(static::$inputObjectType)) {
            return static::$inputObjectType;
        }

        $type = $this->owner->createInputObjectType('RelatedToInputType');
        $type->addIntArgument('element')->lists();
        $type->addBooleanArgument('elementIsEdge');
        $type->addIntArgument('sourceElement')->lists();
        $type->addBooleanArgument('sourceElementIsEdge');
        $type->addIntArgument('targetElement')->lists();
        $type->addBooleanArgument('targetElementIsEdge');
        $type->addStringArgument('field');
        $type->addStringArgument('sourceLocale');
        return static::$inputObjectType = $type;
    }

}