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

        $blockTypes = $field->getBlockTypes();

        foreach ($blockTypes as $blockType) {
            $type = $union->addType(ucfirst($field->handle).ucfirst($blockType->handle), $blockType);
            $type->addFieldsByLayoutId($blockType->fieldLayoutId);
        }

        if (empty($blockTypes)) {
            $warning = 'The matrix field, `'.$field->name.'`, has no block types. This would violate the GraphQL spec so we filled it in with this placeholder.';

            $type = $union->addType(ucfirst($field->handle).'Empty');
            $type->addStringField('empty')
                ->description($warning)
                ->resolve($warning);
        }

        foreach ($union->getTypes() as $typeName => $typeSchema) {
            if (empty($typeSchema->getFields())) {
                $warning = 'The block type, `'.$typeName.'`, has no fields. This would violate the GraphQL spec so we filled it in with this placeholder.';

                $type->addStringField('empty')
                    ->description($warning)
                    ->resolve($warning);
            }
        }

    }
}
