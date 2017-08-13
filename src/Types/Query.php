<?php

namespace markhuot\CraftQL\Types;

use yii\base\Component;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Craft;
use craft\elements\Entry;

class Query extends ObjectType {

    function __construct($request) {
        $token = $request->token();

        $config = [
            'name' => 'Query',
            'fields' => [
                'helloWorld' => [
                    'type' => Type::string(),
                    'resolve' => function ($root, $args) {
                      return 'Welcome to GraphQL! You now have a fully functional GraphQL endpoint.';
                    }
                ],
            ],
        ];

        if ($token->can('query:entries') && $token->allowsMatch('/^query:entryType/')) {
            $config['fields']['entries'] = [
                'type' => Type::listOf(\markhuot\CraftQL\Types\Entry::interface($request)),
                'description' => 'Entries from the craft interface',
                'args' => \markhuot\CraftQL\Types\Entry::args($request),
                'resolve' => $request->entriesCriteria(function ($root, $args) {
                    return \craft\elements\Entry::find();
                }),
            ];
        }

        if ($token->can('query:users')) {
            $config['fields']['users'] = [
                'type' => Type::listOf(\markhuot\CraftQL\Types\User::type($request)),
                'description' => 'Users registered in Craft',
                'args' => \markhuot\CraftQL\Types\User::args(),
                'resolve' => function ($root, $args) {
                    $criteria = \craft\elements\User::find();
                    foreach ($args as $key => $value) {
                        $criteria = $criteria->{$key}($value);
                    }
                    return $criteria->all();
                }
            ];
        }

        if ($token->can('query:sections')) {
            $config['fields']['sections'] = [
                'type' => Type::listOf(\markhuot\CraftQL\Types\Section::type()),
                'description' => 'Sections defined in Craft',
                'args' => [],
                'resolve' => function ($root, $args) {
                    return \Craft::$app->sections->getAllSections();
                }
            ];
        }

        parent::__construct($config);
    }

    static function entriesFieldResolver($criteriaCallback) {
        return function ($root, $args) use ($criteriaCallback) {
            $criteria = $criteriaCallback($root, $args);
            // $criteria->typeId = [1];
            // if (!empty($args['section'])) {
            //     $criteria->sectionId = $args['section'];
            //     unset($args['section']);
            // }
            // if (empty($args['type'])) {
            //     $entryTypeIds = [];
            //     $enum = \markhuot\CraftQL\Types\EntryType::enum();
            //     foreach ($enum->getValues() as $value) {
            //         $entryTypeIds[] = $value->value;
            //     }
            //     $criteria->typeId = $entryTypeIds;
            // }
            // else {
            //     $criteria->typeId = $args['type'];
            //     unset($args['type']);
            // }
            foreach ($args as $key => $value) {
                $criteria = $criteria->{$key}($value);
            }
            return $criteria->all();
        };
    }

}