<?php

namespace markhuot\CraftQL\GraphQLFields\Query;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\GraphQLFields\Base as BaseField;
use markhuot\CraftQL\Types\Entry;

class Entries extends BaseField {

    /**
     * A description of the field, exposed to the GraphQL api
     *
     * @var string
     */
    protected $description = 'A list of entries from Craft';

    /**
     * A callback that can set the base criteria to query. This is useful
     * when you don't want to query all entries, but start with a subset
     * of entries, such as through an Entries field
     *
     * @var callable|boolean
     */
    protected $criteriaCallback = false;

    /**
     * An input object (for argument lists) that controls how any
     * related elements are searched.
     *
     * @var InputObjectType
     */
    static $relatedToInputObject;

    /**
     * The type this field returns
     *
     * @return GraphQLType
     */
    function getType() {
        return Type::listOf(Entry::interface($this->request));
    }

    static function relatedToInputObject() {
        if (static::$relatedToInputObject) {
            return static::$relatedToInputObject;
        }

        return static::$relatedToInputObject = new InputObjectType([
            'name' => 'RelatedTo',
            'fields' => [
                'element' => Type::id(),
                'sourceElement' => Type::id(),
                'targetElement' => Type::id(),
                'field' => Type::string(),
                'sourceLocale' => Type::string(),
            ],
        ]);
    }

    function getArgs() {
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
            'relatedTo' => Type::listOf(static::relatedToInputObject()),
            'orRelatedTo' => Type::listOf(static::relatedToInputObject()),
            'search' => Type::string(),
            'section' => Type::listOf($this->request->sections()->enum()),
            'siblingOf' => Type::int(),
            'slug' => Type::string(),
            'status' => Type::string(),
            'title' => Type::string(),
            'type' => Type::listOf($this->request->entryTypes()->enum()),
            'uri' => Type::string(),
        ];
    }

    function setCriteria($callback) {
        $this->criteriaCallback = $callback;
        return $this;
    }

    function getCriteria($root, $args, $context, $info) {
        if (is_callable($this->criteriaCallback)) {
            $callback = $this->criteriaCallback;
            $criteria = $callback($root, $args, $context, $info);
        }
        else {
            $criteria = \craft\elements\Entry::find();
        }

        return $this->request->entries($criteria, $args, $info);
    }

    function getResolve($root, $args, $context, $info) {
        return $this->getCriteria($root, $args, $context, $info)->all();
    }

}