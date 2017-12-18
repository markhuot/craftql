<?php

namespace markhuot\CraftQL\Listeners;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\UnionType;

class GetMatrixFieldSchema
{
    /**
     * Handle the request for the schema
     *
     * @param \markhuot\CraftQL\Events\GetFieldSchema $event
     * @return void
     */
    function handle($event) {
        $event->handled = true;

        $field = $event->sender;
        $schema = $event->schema;
        $request = $schema->getRequest();

        $union = $schema->addUnionField($field)
            ->lists()
            ->resolveType(function ($root, $args) use ($field) {
                $block = $root->getType();
                return ucfirst($field->handle).ucfirst($block->handle);
            });

        $fieldService = \Yii::$container->get('fieldService');

        foreach ($field->getBlockTypes() as $blockType) {
            $type = $union->addType(ucfirst($field->handle).ucfirst($blockType->handle));
            $type->addFieldsByLayoutId($blockType->fieldLayoutId);
        }
    }

    function getEmptyMatrixObject($field) {
        $msg = 'This matrix has no blocks defined, which GraphQL does not support. A placeholder block was added on the GraphQL side automatically.';

        return new ObjectType([
            'name' => ucfirst($field->handle).'Empty',
            'description' => 'This matrix block is empty',
            'fields' => [
                'empty' => [
                    'type' => Type::string(),
                    'description' => $msg,
                    'resolve' => function ($root, $args) use ($msg) {
                        return $msg;
                    }
                ],
            ],
        ]);
    }

    function getBlockObject($request, $field, $blockType) {
        $fieldService = \Yii::$container->get('fieldService');

        return new ObjectType([
            'name' => ucfirst($field->handle).ucfirst($blockType->handle),
            'fields' => $fieldService->getFields($blockType->fieldLayoutId, $request) ?: [
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
}
