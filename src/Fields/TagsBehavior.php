<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use yii\base\Behavior;

class TagsBehavior extends Behavior
{
    
    public function getGraphQLMutationArgs() {
        $field = $this->owner;

        return [
            $field->handle => ['type' => Type::listOf(Type::int())]
        ];
    }

    public function getGraphQLQueryFields() {
        $field = $this->owner;

        $source = $field->settings['source'];
        if (preg_match('/taggroup:(\d+)/', $source, $matches)) {
            $groupId = $matches[1];

            return [
                $field->handle => [
                    'type' => Type::listOf(\markhuot\CraftQL\Repositories\TagGroups::getGroup($groupId)),
                    'description' => $field->instructions,
                ]
            ];
        }
    }

    public function upsert($values) {
        return $values;
    }

}