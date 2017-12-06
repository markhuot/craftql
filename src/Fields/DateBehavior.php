<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use yii\base\Behavior;

class DateBehavior extends Behavior
{

    public function getGraphQLMutationArgs() {
        $field = $this->owner;

        return [
            $field->handle => ['type' => Type::int()]
        ];
    }

    public function getGraphQLQueryFields($token) {
        $fieldService = \Yii::$container->get('fieldService');
        return $fieldService->getDateFieldDefinition($this->owner->handle, $this->owner->instructions, !!$this->owner->required);
    }

    public function upsert($value) {
        return $value;
    }

}