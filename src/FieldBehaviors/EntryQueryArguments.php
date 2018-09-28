<?php

namespace markhuot\CraftQL\FieldBehaviors;

use markhuot\CraftQL\Behaviors\FieldBehavior;
use markhuot\CraftQL\Builders\Field;
use markhuot\CraftQL\Builders\InputSchema;

class EntryQueryArguments extends FieldBehavior {

    static $inputObjectType;

    static $cache;

    function initEntryQueryArguments() {
        if (!empty(static::$cache)) {
            $this->owner->addArguments(static::$cache);
            return;
        }

        $tmp = new Field($this->owner->request, 'tmp');

        $tmp->addStringArgument('after');
        $tmp->addIntArgument('ancestorOf');
        $tmp->addIntArgument('ancestorDist');
        $tmp->addBooleanArgument('archived');
        $tmp->addStringArgument('authorGroup');
        $tmp->addIntArgument('authorGroupId');
        $tmp->addIntArgument('authorId')->lists();
        $tmp->addStringArgument('before');
        $tmp->addIntArgument('level');
        $tmp->addBooleanArgument('localeEnabled');
        $tmp->addIntArgument('descendantOf');
        $tmp->addIntArgument('descendantDist');
        $tmp->addBooleanArgument('fixedOrder');
        $tmp->addIntArgument('id')->lists();
        $tmp->addIntArgument('idNot')->lists();
        $tmp->addIntArgument('limit');
        $tmp->addStringArgument('site');
        $tmp->addIntArgument('siteId');
        $tmp->addIntArgument('nextSiblingOf');
        $tmp->addIntArgument('offset');
        $tmp->addStringArgument('order');
        $tmp->addStringArgument('orderBy');
        $tmp->addIntArgument('positionedAfter');
        $tmp->addIntArgument('positionedBefore');
        $tmp->addStringArgument('postDate');
        $tmp->addIntArgument('prevSiblingOf');
        $tmp->addStringArgument('relatedTo')->lists()->type($this->relatedToInputObject());
        $tmp->addStringArgument('orRelatedTo')->lists()->type($this->relatedToInputObject());
        $tmp->addStringArgument('search');
        $tmp->addStringArgument('section')->lists()->type($tmp->getRequest()->sections()->enum());
        $tmp->addIntArgument('siblingOf');
        $tmp->addStringArgument('slug');
        $tmp->addStringArgument('status');
        $tmp->addStringArgument('title');
        $tmp->addStringArgument('type')->lists()->type($tmp->getRequest()->entryTypes()->enum());
        $tmp->addStringArgument('uri');

        $fieldService = \Yii::$container->get('craftQLFieldService');
        $arguments = $fieldService->getQueryArguments($tmp->getRequest());
        $tmp->addArguments($arguments, false);

        $this->owner->addArguments(static::$cache = $tmp->getArguments());
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