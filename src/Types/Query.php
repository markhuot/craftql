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
                $config['fields']['entries'] = (new \markhuot\CraftQL\GraphQLFields\Query\Entries($request))->toArray();
                $config['fields']['entriesConnection'] = (new \markhuot\CraftQL\GraphQLFields\Query\EntriesConnection($request))->toArray();
                $config['fields']['entry'] = (new \markhuot\CraftQL\GraphQLFields\Query\Entry($request))->toArray();
                $config['fields']['drafts'] = (new \markhuot\CraftQL\GraphQLFields\Query\Drafts($request))->toArray();
            }
        }

        if ($token->can('query:tags')) {
            $config['fields']['tags'] = (new \markhuot\CraftQL\GraphQLFields\Query\Tags($request))->toArray();
            $config['fields']['tagsConnection'] = (new \markhuot\CraftQL\GraphQLFields\Query\TagsConnection($request))->toArray();
        }

        if ($token->can('query:categories')) {
            $config['fields']['categories'] = (new \markhuot\CraftQL\GraphQLFields\Query\Categories($request))->toArray();
            $config['fields']['categoriesConnection'] = (new \markhuot\CraftQL\GraphQLFields\Query\CategoriesConnection($request))->toArray();
        }

        if ($token->can('query:users')) {
            $config['fields']['users'] = (new \markhuot\CraftQL\GraphQLFields\Query\Users($request))->toArray();
        }

        if ($token->can('query:sections')) {
            $config['fields']['sections'] = (new \markhuot\CraftQL\GraphQLFields\Query\Sections($request))->toArray();
        }

        parent::__construct($config);
    }

}