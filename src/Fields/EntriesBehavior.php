<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\UnionType;
use yii\base\Behavior;

class EntriesBehavior extends Behavior
{
    public function getGraphQLMutationArgs() {
        $field = $this->owner;

        return [
            $field->handle => ['type' => Type::listOf(Type::int())]
        ];
    }

    public function getCriteria($root, $args, $context, $info) {
        $field = $this->owner;
        return $root->{$field->handle};
    }

    public function getGraphQLQueryFields($request) {
        $field = $this->owner;

        return [
            $field->handle => (new \markhuot\CraftQL\GraphQLFields\Query\Entries($request))->setCriteria([$this, 'getCriteria'])->toArray(),
            "{$field->handle}Connection" => (new \markhuot\CraftQL\GraphQLFields\Query\EntriesConnection($request))->setCriteria([$this, 'getCriteria'])->toArray(),
        ];
    }

    public function upsert($values) {
        return $values;
    }

}