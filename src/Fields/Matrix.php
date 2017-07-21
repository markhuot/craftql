<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\UnionType;
use GraphQL\Type\Definition\Type;
use yii\base\Component;

class Matrix extends Component {

    private $tagGroups;
    static $objects = [];

    function getGraphQlObject($field) {
        if (isset(static::$objects[$field->handle])) {
            return static::$objects[$field->handle];
        }
        
        $fieldService = \Yii::$container->get(\markhuot\CraftQL\Services\FieldService::class);

        $blockObjects = [];
        foreach ($field->getBlockTypes() as $block) {
            $blockObjects[] = new ObjectType([
                'name' => ucfirst($field->handle).'Matrix'.ucfirst($block->handle),
                'fields' => $fieldService->getFields($block->fieldLayoutId),
            ]);
        }

        return static::$objects[$field->handle] = new UnionType([
            'name' => ucfirst($field->handle).'Matrix',
            'types' => $blockObjects,
            'resolveType' => function ($root, $args) use ($field) {
                $block = $root->getType();
                return ucfirst($field->handle).'Matrix'.ucfirst($block->handle);
            },
        ]);
    }

    function getDefinition($field) {
        return [$field->handle => [
            'type' => Type::listOf($this->getGraphQlObject($field)),
            'description' => $field->instructions,
        ]];
    }

}
