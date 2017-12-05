<?php

namespace markhuot\CraftQL\GraphQLFields\Query;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\GraphQLFields\Base as BaseField;
use markhuot\CraftQL\Types\Entry;
use markhuot\CraftQL\Types\Tag;
use markhuot\CraftQL\GraphQLFields\Query\Entries as EntriesField;

class Tags extends BaseField {

    protected $description = 'Tags in Craft';

    function getType() {
        return Type::listOf(Tag::interface($this->request));
    }

    function getArgs() {
        return [
            'fixedOrder' => Type::boolean(),
            'group' => $this->request->tagGroups()->enum(),
            'groupId' => Type::int(),
            'id' => Type::int(),
            'indexBy' => Type::string(),
            'limit' => Type::int(),
            'locale' => Type::string(),
            'offset' => Type::int(),
            'order' => Type::string(),
            'relatedTo' => Type::listOf(EntriesField::relatedToInputObject()),
            'search' => Type::string(),
            'slug' => Type::string(),
            'title' => Type::string(),
        ];
    }

    function getCriteria($root, $args, $context, $info) {
        $criteria = \craft\elements\Tag::find();

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