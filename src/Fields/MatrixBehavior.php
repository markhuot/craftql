<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\UnionType;
use GraphQL\Type\Definition\Type;
use yii\base\Behavior;

class MatrixBehavior extends Behavior
{
    static $objects = [];
    
    public function getGraphQLMutationArgs() {
        $field = $this->owner;

        return [
            $field->handle => ['type' => Type::string()]
        ];
    }

    function getGraphQlObject($token, $field) {
        if (isset(static::$objects[$field->handle])) {
            return static::$objects[$field->handle];
        }
        
        $fieldService = \Yii::$container->get(\markhuot\CraftQL\Services\FieldService::class);

        $blockObjects = [];
        foreach ($field->getBlockTypes() as $block) {
            $blockObjects[] = new ObjectType([
                'name' => ucfirst($field->handle).ucfirst($block->handle),
                'fields' => $fieldService->getFields($block->fieldLayoutId, $token),
            ]);
        }

        return static::$objects[$field->handle] = new UnionType([
            'name' => ucfirst($field->handle).'Matrix',
            'description' => 'A union of possible blocks for this matrix field',
            'types' => $blockObjects,
            'resolveType' => function ($root, $args) use ($field) {
                $block = $root->getType();
                return ucfirst($field->handle).ucfirst($block->handle);
            },
        ]);
    }

    public function getGraphQLQueryFields($token) {
        $field = $this->owner;

        return [
            $field->handle => [
                'type' => Type::listOf($this->getGraphQlObject($token, $field)),
                'description' => $field->instructions,
            ]
        ];
    }

    public function upsert($value) {
        return $value;
    }

}