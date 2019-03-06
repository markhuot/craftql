<?php

namespace markhuot\CraftQL\FieldBehaviors;

use markhuot\CraftQL\Behaviors\FieldBehavior;
use markhuot\CraftQL\Builders\Field;
use markhuot\CraftQL\Builders\InputSchema;
use markhuot\CraftQL\Types\EntryTypesEnum;
use markhuot\CraftQL\Types\RelatedToInputType;
use markhuot\CraftQL\Types\SectionsEnum;
use markhuot\CraftQL\Types\SitesEnum;

class EntryQueryArguments extends FieldBehavior {

    static $args = null;
    static $inputObjectType;

    function initEntryQueryArguments() {
        if (static::$args !== null) {
            $this->owner->addArguments(static::$args, false);
            return;
        }

        $tmp = new Field($this->owner->request, 'TmpHoldingForAllQueryArgs');

        $tmp->addStringArgument('after');
        $tmp->addIntArgument('ancestorDist');
        $tmp->addIntArgument('ancestorOf');
        $tmp->addBooleanArgument('anyStatus');
        $tmp->addBooleanArgument('archived');
        $tmp->addStringArgument('authorGroup')->lists();
        $tmp->addIntArgument('authorGroupId')->lists();
        $tmp->addIntArgument('authorId')->lists();
        $tmp->addStringArgument('before');
        $tmp->addStringArgument('dateCreated');
        $tmp->addStringArgument('dateUpdated');
        $tmp->addIntArgument('descendantDist');
        $tmp->addIntArgument('descendantOf');
        $tmp->addBooleanArgument('enabledForSite');
        $tmp->addStringArgument('expiryDate');
        $tmp->addBooleanArgument('fixedOrder');
        $tmp->addBooleanArgument('hasDescendants');
        // $tmp->addBooleanArgument('localeEnabled');
        $tmp->addIntArgument('id')->lists();
        $tmp->addIntArgument('idNot')->lists();
        $tmp->addBooleanArgument('inReverse');
        $tmp->addBooleanArgument('leaves');
        $tmp->addIntArgument('level');
        $tmp->addIntArgument('limit');
        $tmp->addIntArgument('nextSiblingOf');
        $tmp->addIntArgument('offset');
        $tmp->addStringArgument('order');
        $tmp->addStringArgument('orderBy');
        $tmp->addIntArgument('positionedAfter');
        $tmp->addIntArgument('positionedBefore');
        $tmp->addStringArgument('postDate');
        $tmp->addIntArgument('prevSiblingOf');
        $tmp->addArgument('relatedTo')->lists()->type(RelatedToInputType::class);
        $tmp->addArgument('orRelatedTo')->lists()->type(RelatedToInputType::class);
        $tmp->addStringArgument('search');
        $tmp->addStringArgument('section')->lists()->type(SectionsEnum::class);
        $tmp->addIntArgument('siblingOf');
        $tmp->addArgument('site')->type(SitesEnum::class);
        $tmp->addIntArgument('siteId');
        $tmp->addStringArgument('slug')->lists();
        $tmp->addStringArgument('status')->lists();
        $tmp->addStringArgument('title')->lists();
        $tmp->addBooleanArgument('trashed');
        $tmp->addStringArgument('type')->lists()->type(EntryTypesEnum::class);
        $tmp->addStringArgument('uid');
        $tmp->addStringArgument('uri');

        $fieldService = \Yii::$container->get('craftQLFieldService');
        $arguments = $fieldService->getQueryArguments($tmp->getRequest());
        $tmp->addArguments($arguments, false);
        static::$args = $tmp->getArguments();

        $this->owner->addArguments(static::$args, false);
        return static::$args;
    }

}