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
     * The type this field returns
     *
     * @return GraphQLType
     */
    function getType() {
        return Type::listOf(Entry::interface($this->request));
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