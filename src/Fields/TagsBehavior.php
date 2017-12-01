<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;
use yii\base\Behavior;
use craft\elements\Tag;
use Craft;

class TagsBehavior extends Behavior
{

    static $inputs = [];

    function getGraphQLInput($field) {
        if (isset(static::$inputs[$field->handle])) {
            return static::$inputs[$field->handle];
        }

        return static::$inputs[$field->handle] = new InputObjectType([
            'name' => ucfirst($field->handle).'TagInput',
            'fields' => [
                'id' => Type::int(),
                'title' => Type::string(),
            ],
        ]);
    }

    public function getGraphQLMutationArgs() {
        $field = $this->owner;

        return [
            $field->handle => ['type' => Type::listOf($this->getGraphQLInput($field))]
        ];
    }

    public function getGraphQLQueryFields($request) {
        $field = $this->owner;

        if (!$request->token()->can('query:tags')) {
            return [];
        }

        $source = $field->settings['source'];
        if (preg_match('/taggroup:(\d+)/', $source, $matches)) {
            $groupId = $matches[1];

            return [
                $field->handle => [
                    'type' => Type::listOf($request->tagGroup($groupId)),
                    'description' => $field->instructions,
                    'resolve' => function ($root, $args) use ($field) {
                        return $root->{$field->handle}->all();
                    }
                ]
            ];
        }

        return [];
    }

    public function upsert($values) {
        return array_map(function ($value) {
            if ($id = @$value['id']) {
                return $id;
            }

            $field = $this->owner;
            preg_match('/taggroup:(\d+)/', $field->source, $matches);
            if (!$matches) {
                return;
            }

            $groupId = $matches[1];
            $group = Craft::$app->tags->getTagGroupById($groupId);

            $tag = new Tag();
            $tag->groupId = $groupId;
            $tag->fieldLayoutId = $group->fieldLayoutId;
            $tag->title = trim(@$value['title']);
            Craft::$app->getElements()->saveElement($tag);

            return $tag->id;
        }, $values);
    }

}