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

        $fieldService = \Yii::$container->get('fieldService');

        $blockObjects = [];
        foreach ($field->getBlockTypes() as $block) {
            $blockObjects[] = new ObjectType([
                'name' => ucfirst($field->handle).ucfirst($block->handle),
                'fields' => $fieldService->getFields($block->fieldLayoutId, $token) ?: [
                    'EMPTY' => [
                        'type' => Type::string(),
                        'description' => 'This block has no fields defined, which GraphQL does not support. A placeholder field was added on the GraphQL side automatically.',
                        'resolve' => function ($root, $args) {
                            return 'This block has no fields defined, which GraphQL does not support. A placeholder field was added on the GraphQL side automatically.';
                        }
                ],
                ],
            ]);
        }

        if (empty($blockObjects)) {
            $blockObjects[] = new ObjectType([
                'name' => ucfirst($field->handle).'Empty',
                'description' => 'This matrix block is empty',
                'fields' => [
                    'empty' => [
                        'type' => Type::string(),
                        'description' => 'This matrix has no blocks defined, which GraphQL does not support. A placeholder block was added on the GraphQL side automatically.',
                        'resolve' => function ($root, $args) {
                            return 'This matrix has no blocks defined, which GraphQL does not support. A placeholder block was added on the GraphQL side automatically.';
                        }
                    ],
                ],
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