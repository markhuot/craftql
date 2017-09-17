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
            if (!empty($request->entryTypes()->all())) {
                $config['fields']['entries'] = [
                    'type' => Type::listOf(\markhuot\CraftQL\Types\Entry::interface($request)),
                    'description' => 'A list of entries from Craft',
                    'args' => \markhuot\CraftQL\Types\Entry::args($request),
                    'resolve' => function ($root, $args, $context, $info) use ($request) {
                        return $request->entries(\craft\elements\Entry::find(), $args, $info)->all();
                    },
                ];

                $config['fields']['entriesConnection'] = [
                    'type' => \markhuot\CraftQL\Types\EntryConnection::type($request),
                    'description' => 'A connection to entries in Craft',
                    'args' => \markhuot\CraftQL\Types\Entry::args($request),
                    'resolve' => function ($root, $args, $context, $info) use ($request) {
                        $criteria = $request->entries(\craft\elements\Entry::find(), $args, $info);
                        list($pageInfo, $entries) = \craft\helpers\Template::paginateCriteria($criteria);

                        return [
                            'totalCount' => $pageInfo->total,
                            'pageInfo' => $pageInfo,
                            'edges' => $entries,
                            'criteria' => $criteria,
                            'args' => $args,
                        ];
                    }
                ];

                $config['fields']['entry'] = [
                    'type' => \markhuot\CraftQL\Types\Entry::interface($request),
                    'description' => 'One entry from Craft',
                    'args' => \markhuot\CraftQL\Types\Entry::args($request),
                    'resolve' => function ($root, $args, $context, $info) use ($request) {
                        return $request->entries(\craft\elements\Entry::find(), $args, $info)->one();
                    },
                ];
            }
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

}