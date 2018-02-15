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

        $union = $schema->addUnionField($field)
            ->lists()
            ->resolveType(function ($root, $args) use ($field) {
                $block = $root->getType();
                return ucfirst($field->handle).ucfirst($block->handle);
            })
            ->resolve(function ($root, $args, $context, $info) use ($field) {
                return $root->{$field->handle}->all();
            });

        $blockTypes = $field->getBlockTypes();

        foreach ($blockTypes as $blockType) {
            $type = $union->addType(ucfirst($field->handle).ucfirst($blockType->handle), $blockType);
            $type->addIntField('id');
            $type->addFieldsByLayoutId($blockType->fieldLayoutId);

            if (empty($type->getFields())) {
                $warning = 'The block type, `'.$blockType->handle.'` on `'.$field->handle.'`, has no fields. This would violate the GraphQL spec so we filled it in with this placeholder.';

                $type->addStringField('empty')
                    ->description($warning)
                    ->resolve($warning);
            }
        }

        if (empty($blockTypes)) {
            $warning = 'The matrix field, `'.$field->name.'`, has no block types. This would violate the GraphQL spec so we filled it in with this placeholder.';

            $type = $union->addType(ucfirst($field->handle).'Empty');
            $type->addStringField('empty')
                ->description($warning)
                ->resolve($warning);
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
