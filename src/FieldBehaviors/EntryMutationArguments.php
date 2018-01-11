<?php

namespace markhuot\CraftQL\FieldBehaviors;

use yii\base\Behavior;
use GraphQL\Type\Definition\Type;
use craft\elements\Entry;
use markhuot\CraftQL\Builders\Field;
use Craft;

class EntryMutationArguments extends Behavior {

    /**
     * @var Field|null the owner of this behavior
     */
    public $owner;

    function initEntryMutationArguments() {
        $this->owner->addIntArgument('id');
        $this->owner->addIntArgument('authorId');
        $this->owner->addStringArgument('title');

        $fieldLayoutId = $this->owner->getType()->getContext()->fieldLayoutId;
        $this->owner->addArgumentsByLayoutId($fieldLayoutId);

        $this->owner->resolve(function ($root, $args) {
            if (!empty($args['id'])) {
                $criteria = Entry::find();
                $criteria->id($args['id']);
                $entry = $criteria->one();
                if (!$entry) {
                    throw new \GraphQL\Error\UserError('Could not find an entry with id '.$args['id']);
                }
            }
            else {
                $entry = new Entry();
                $entry->sectionId = $this->owner->getType()->getContext()->section->id;
                $entry->typeId = $this->owner->getType()->getContext()->id;
            }

            if (isset($args['authorId'])) {
                $entry->authorId = $args['authorId'];
            }

            if (isset($args['title'])) {
                $entry->title = $args['title'];
            }

            $fields = $args;
            unset($fields['id']);
            unset($fields['title']);
            unset($fields['sectionId']);
            unset($fields['typeId']);
            unset($fields['authorId']);

            $fieldService = \Yii::$container->get('craftQLFieldService');

            foreach ($fields as $handle => &$value) {
                $callback = $this->owner->getArgument($handle)->getOnSave();
                if ($callback) {
                    $value = $callback($value);
                }
            }

            $entry->setFieldValues($fields);

            Craft::$app->elements->saveElement($entry);

            return $entry;
        });
    }

}