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

        $fieldService = \Yii::$container->get('craftQLFieldService');

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

        if (!empty($blockTypes)) {
            $inputType = $event->mutation->createInputObjectType(ucfirst($event->sender->handle) . 'Input');

            foreach ($blockTypes as $blockType) {
                $blockInputType = $event->mutation->createInputObjectType(ucfirst($event->sender->handle) . ucfirst($blockType->handle) . 'Input');
                $blockInputType->addArgumentsByLayoutId($blockType->fieldLayoutId);
                $blockInputType->addStringArgument('foo');

                $inputType->addArgument($blockType->handle)
                    ->type($blockInputType);
            }

            $event->mutation->addArgument($event->sender)
                ->lists()
                ->type($inputType)
                ->onSave(function ($values) {
                    $newValues = [];

                    foreach ($values as $key => $value) {
                        $type = array_keys($value)[0];

                        $newValues["new{$key}"] = [
                            'type' => $type,
                            'enabled' => 1,
                            'fields' => $value[$type],
                        ];
                    }

                    return $newValues;
                });
        }

    }
}
