<?php

namespace markhuot\CraftQL\Types;

use yii\base\Component;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Craft;
use craft\elements\Entry;

class Query extends ObjectType {

    function __construct($token) {
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
                'type' => Type::listOf(\markhuot\CraftQL\Types\Entry::interface()),
                'description' => 'Entries from the craft interface',
                'args' => \markhuot\CraftQL\Types\Entry::args($token),
                'resolve' => function ($root, $args) use ($token) {
                    $criteria = \craft\elements\Entry::find();
                    if (empty($args['type'])) {
                        $criteria->typeId = $token->queryableEntryTypeIds();
                    }
                    foreach ($args as $key => $value) {
                        $criteria = $criteria->{$key}($value);
                    }
                    return $criteria->all();
                }
            ];
        }

        if ($token->can('query:users')) {
            $config['fields']['users'] = [
                'type' => Type::listOf(\markhuot\CraftQL\Types\User::type()),
                'description' => 'Entries from the craft interface',
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

        parent::__construct($config);
    }

}