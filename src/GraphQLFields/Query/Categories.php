<?php

namespace markhuot\CraftQL\GraphQLFields\Query;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\GraphQLFields\Base as BaseField;
use markhuot\CraftQL\Types\Category;
use markhuot\CraftQL\Types\Entry;
use markhuot\CraftQL\GraphQLFields\Query\Entries as EntriesField;

class Categories extends BaseField {

    protected $description = 'Categories in Craft';

    function getType() {
        return Type::listOf(Category::interface($this->request));
    }

    function getArgs() {
        return [
            'ancestorOf' => Type::int(),
            'ancestorDist' => Type::int(),
            'level' => Type::int(),
            'descendantOf' => Type::int(),
            'descendantDist' => Type::int(),
            'fixedOrder' => Type::boolean(),
            'group' => $this->request->categoryGroups()->enum(),
            'groupId' => Type::int(),
            'id' => Type::int(),
            'indexBy' => Type::string(),
            'limit' => Type::int(),
            'locale' => Type::string(),
            'nextSiblingOf' => Type::int(),
            'offset' => Type::int(),
            'order' => Type::string(),
            'positionedAfter' => Type::int(),
            'positionedBefore' => Type::int(),
            'prevSiblingOf' => Type::int(),
            'relatedTo' => Type::listOf(EntriesField::relatedToInputObject()),
            'search' => Type::string(),
            'siblingOf' => Type::int(),
            'slug' => Type::string(),
            'title' => Type::string(),
            'uri' => Type::string(),
        ];
    }

    protected function getCriteria($root, $args, $context, $info) {
        $criteria = \craft\elements\Category::find();

        if (isset($args['group'])) {
            $args['groupId'] = $args['group'];
            unset($args['group']);
        }

        foreach ($args as $key => $value) {
            $criteria = $criteria->{$key}($value);
        }

        return $criteria;
    }

    function getResolve($root, $args, $context, $info) {
        return $this->getCriteria($root, $args, $context, $info)->all();
    }

}