<?php

namespace markhuot\CraftQL\Listeners;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\UnionType;
use markhuot\CraftQL\Builders\Argument;
use markhuot\CraftQL\Builders\Field;
use markhuot\CraftQL\Builders\InputSchema;

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
            ->arguments(function ($field) {
                $field->addStringArgument('type');
                $field->addIntArgument('limit');
            })
            ->resolveType(function ($root, $args) use ($field) {
                $block = $root->getType();
                return ucfirst($field->handle).ucfirst($block->handle);
            })
            ->resolve(function ($root, $args, $context, $info) use ($field) {
                if (!empty($args['type'])) {
                    $query = $root->{$field->handle}->type($args['type']);
                }
                else {
                    $query = $root->{$field->handle};
                }

                if (!empty($args['limit'])) {
                    $query = $query->limit($args['limit']);
                }

                return $query->all();
            });

        $blockTypes = $field->getBlockTypes();

        foreach ($blockTypes as $blockType) {
            $type = $union->addType(ucfirst($field->handle).ucfirst($blockType->handle), $blockType);
            $type->addStringField('id'); // ideally this would be an `int`, but draft matrix blocks have an id of `new1`
            $type->addBooleanField('enabled');
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
            $inputType->addStringArgument('id');
            $inputType->addBooleanArgument('enabled');

            foreach ($blockTypes as $blockType) {
                $blockInputType = $event->mutation->createInputObjectType(ucfirst($event->sender->handle) . ucfirst($blockType->handle) . 'Input');
                $blockInputType->addArgumentsByLayoutId($blockType->fieldLayoutId);

                if (count($blockInputType->getArguments()) == 0) {
                    $blockInputType->addStringArgument('emptyEntrytType')->description('The entry type, '.$event->sender->handle.', has no fields. This would violate the GraphQL spec so we filled it in with this placeholder.');
                }

                $inputType->addArgument($blockType->handle)
                    ->type($blockInputType);
            }

            $event->mutation->addArgument($event->sender)
                ->lists()
                ->type($inputType)
                ->onSave(function ($values) use ($inputType) {
                    $newValues = [];

                    foreach ($values as $index => $value) {
                        $id = @$value['id'] ? $value['id'] : "new{$index}";
                        $enabled = @$value['enabled'] ? $value['enabled']: 0;
                        unset($value['id']);
                        if (isset($value['type'])) {
                            $type = $value['type'];
                            $fields = $value['fields'];
                        }
                        else {
                            // get the keys
                            $keys = array_keys($value);
                            // remove known keys
                            $keys = array_flip($keys);
                            unset($keys['id']);
                            unset($keys['enabled']);
                            unset($keys['type']);
                            unset($keys['fields']);
                            $keys = array_merge(array_flip($keys));
                            // type is the only remaining key
                            $type = $keys[0];
                            $fields = $value[$type];
                        }

                        foreach ($fields as $fieldHandle => &$fieldValue) {
                            /** @var Argument $blockArgument */
                            $blockArgument = $inputType->getArgument($type);
                            /** @var InputSchema $blockType */
                            $blockType = $blockArgument->getType();
                            $callback = $blockType->getArgument($fieldHandle)->getOnSave();
                            if ($callback) {
                                $fieldValue = $callback($fieldValue);
                            }
                        }

                        $newValues[$id] = [
                            'type' => $type,
                            'enabled' => $enabled,
                            'fields' => $fields,
                        ];
                    }

                    return $newValues;
                });
        }

    }
}
