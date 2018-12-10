<?php

namespace markhuot\CraftQL\FieldBehaviors;

use craft\base\Element;
use craft\elements\db\EntryQuery;
use craft\elements\Entry;
use craft\helpers\DateTimeHelper;
use markhuot\CraftQL\Behaviors\FieldBehavior;
use markhuot\CraftQL\Builders\Field;
use Craft;

class EntryMutationArguments extends FieldBehavior {

    /**
     * @var Field the owner of this behavior
     */
    public $owner;

    function initEntryMutationArguments() {
        $this->owner->addIntArgument('id');
        $this->owner->addIntArgument('siteId');
        $this->owner->addIntArgument('authorId');
        $this->owner->addStringArgument('title');
        $this->owner->addStringArgument('slug');
        $this->owner->addBooleanArgument('enabled');
        $this->owner->addDateArgument('postDate');
        $this->owner->addDateArgument('expiryDate');
        $this->owner->addIntArgument('parentId');

        $mutationQueryObject = $this->owner->createInputObjectType('MutationQuery');
        $mutationQueryObject->use(new EntryQueryArguments);
        $this->owner->addArgument('query')->type($mutationQueryObject);

        $fieldLayoutId = $this->owner->getType()->getContext()->fieldLayoutId;
        $this->owner->addArgumentsByLayoutId($fieldLayoutId);

        $this->owner->resolve(function ($root, $args, $context, $info) {
            if (!empty($args['id'])) {
                $criteria = Entry::find();
                $criteria->id($args['id']);
                if (!empty($args['siteId']))  {
                    $criteria->siteId($args['siteId']);
                }
                $entry = $criteria->one();
                if (!$entry) {
                    throw new \GraphQL\Error\UserError('Could not find an entry with id '.$args['id']);
                }
            }
            else if (!empty($args['query'])) {
                $criteria = $this->owner->getRequest()->entries(Entry::find(), $root, $args['query'], $context, $info);
                $entry = $criteria->one();
            }

            if (empty($entry)) {
                $entry = new Entry();
                $entry->sectionId = $this->owner->getType()->getContext()->section->id;
                $entry->typeId = $this->owner->getType()->getContext()->id;
                $entry->fieldLayoutId = $this->owner->getType()->getContext()->fieldLayoutId;
            }

            if (isset($args['siteId'])) {
                $entry->siteId = $args['siteId'];
            }

            if (isset($args['authorId'])) {
                $entry->authorId = $args['authorId'];
            }
            else if (empty($args['authorId']) && empty($entry->authorId) && !empty($this->owner->getRequest()->token()->user)) {
                $entry->authorId = $this->owner->getRequest()->token()->user->id;
            }

            if (isset($args['title'])) {
                $entry->title = $args['title'];
            }

            if (isset($args['slug'])) {
                $entry->slug = $args['slug'];
            }

            if (isset($args['enabled'])) {
                $entry->enabled = $args['enabled'];
            }

            if (isset($args['parentId'])) {
                $entry->newParentId = $args['parentId'];
            }

            if (isset($args['postDate'])) {
                $entry->postDate = DateTimeHelper::toDateTime($args['postDate']);
            }

            if (isset($args['expiryDate'])) {
                $entry->expiryDate = DateTimeHelper::toDateTime($args['expiryDate']);
            }

            $fields = $args;
            unset($fields['id']);
            unset($fields['siteId']);
            unset($fields['title']);
            unset($fields['slug']);
            unset($fields['sectionId']);
            unset($fields['typeId']);
            unset($fields['authorId']);
            unset($fields['enabled']);
            unset($fields['parentId']);
            unset($fields['postDate']);
            unset($fields['expiryDate']);
            unset($fields['query']);

            $fieldService = \Yii::$container->get('craftQLFieldService');

            foreach ($fields as $handle => &$value) {
                $callback = $this->owner->getArgument($handle)->getOnSave();
                if ($callback) {
                    $value = $callback($value);
                }
            }

            $entry->setFieldValues($fields);

            $entry->setScenario(Element::SCENARIO_LIVE);

            if (!Craft::$app->elements->saveElement($entry)) {
                $errorStrings = [];

                foreach ($entry->errors as $fieldName => $errors) {
                    $errorStrings = array_merge($errorStrings, $errors);
                }
                throw new \GraphQL\Error\UserError('Validation failed.'."\n\n- ".implode("\n-", $errorStrings));
            }

            return $entry;
        });
    }

}