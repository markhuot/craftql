<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Builders\Schema;

class Volume extends Schema {

    protected $interfaces = [
        \markhuot\CraftQL\Types\VolumeInterface::class,
    ];

    function boot() {
        $this->addFieldsByLayoutId($this->context->fieldLayoutId);
    }

    function getName(): string {
        return ucfirst($this->context->handle).'Volume';
    }

    // /**
    //  * An input object (for argument lists) that controls how any
    //  * related elements are searched.
    //  *
    //  * @var InputObjectType
    //  */
    // static $relatedToInputObject;

    // static function baseInputArgs() {
    //     return [
    //         'id' => ['type' => Type::int()],
    //         'authorId' => ['type' => Type::int()],
    //         'title' => ['type' => Type::string()],
    //     ];
    // }

    // /**
    //  * An input object to query entries by relationship
    //  *
    //  * @return InputObjectType
    //  */
    // static function relatedToInputObject() {
    //     if (static::$relatedToInputObject) {
    //         return static::$relatedToInputObject;
    //     }

    //     return static::$relatedToInputObject = new InputObjectType([
    //         'name' => 'RelatedTo',
    //         'fields' => [
    //             'element' => Type::id(),
    //             'sourceElement' => Type::id(),
    //             'targetElement' => Type::id(),
    //             'field' => Type::string(),
    //             'sourceLocale' => Type::string(),
    //         ],
    //     ]);
    // }

    static function args($request) {
        return [
            'after' => Type::string(),
            'ancestorOf' => Type::int(),
            'ancestorDist' => Type::int(),
            'archived' => Type::boolean(),
            'authorGroup' => Type::string(),
            'authorGroupId' => Type::int(),
            'authorId' => Type::listOf(Type::int()),
            'before' => Type::string(),
            'level' => Type::int(),
            'localeEnabled' => Type::boolean(),
            'descendantOf' => Type::int(),
            'descendantDist' => Type::int(),
            'fixedOrder' => Type::boolean(),
            'id' => Type::listOf(Type::int()),
            'limit' => Type::int(),
            'locale' => Type::string(),
            'nextSiblingOf' => Type::int(),
            'offset' => Type::int(),
            'order' => Type::string(),
            'positionedAfter' => Type::id(),
            'positionedBefore' => Type::id(),
            'postDate' => Type::string(),
            'prevSiblingOf' => Type::id(),
            // 'relatedTo' => Type::listOf(static::relatedToInputObject()),
            // 'orRelatedTo' => Type::listOf(static::relatedToInputObject()),
            'search' => Type::string(),
            'section' => Type::listOf($request->sections()->enum()),
            'siblingOf' => Type::int(),
            'slug' => Type::string(),
            'status' => Type::string(),
            'title' => Type::string(),
            // 'type' => Type::listOf($request->entryTypes()->enum()),
            'uri' => Type::string(),
        ];
    }

    // function getGraphQLMutationArgs($request) {
    //     $fieldService = \Yii::$container->get('fieldService');

    //     return array_merge(\markhuot\CraftQL\Types\Entry::baseInputArgs(), $fieldService->getGraphQLMutationArgs($this->config['craftType']->fieldLayoutId, $request));
    // }

    // function handle() {
    //     return $this->config['craftType']->handle;
    // }

    // function upsert($request) {
    //     return function ($root, $args) use ($request) {
    //         if (!empty($args['id'])) {
    //             $criteria = Entry::find();
    //             $criteria->id($args['id']);
    //             $entry = $criteria->one();
    //             if (!$entry) {
    //                 throw new \Exception('Could not find an entry with id '.$args['id']);
    //             }
    //         }
    //         else {
    //             $entry = new Entry();
    //             $entry->sectionId = $this->config['craftType']->section->id;
    //             $entry->typeId = $this->config['craftType']->id;
    //         }

    //         if (isset($args['authorId'])) {
    //             $entry->authorId = $args['authorId'];
    //         }

    //         if (isset($args['title'])) {
    //             $entry->title = $args['title'];
    //         }

    //         $fields = $args;
    //         unset($fields['id']);
    //         unset($fields['title']);
    //         unset($fields['sectionId']);
    //         unset($fields['typeId']);
    //         unset($fields['authorId']);

    //         $fieldService = \Yii::$container->get('fieldService');

    //         foreach ($fields as $handle => &$value) {
    //             $field = Craft::$app->fields->getFieldByHandle($handle);
    //             $value = $fieldService->mutateValueForField($request, $field, $value, $entry);
    //             // $value = $field->upsert($value, $entry);
    //         }

    //         $entry->setFieldValues($fields);

    //         Craft::$app->elements->saveElement($entry);

    //         return $entry;
    //     };
    // }

}