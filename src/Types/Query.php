<?php

namespace markhuot\CraftQL\Types;

use yii\base\Component;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Craft;
use markhuot\CraftQL\Builders\Schema;
use markhuot\CraftQL\Types\Entry;
use markhuot\CraftQL\Types\EntryDraft;
use markhuot\CraftQL\Types\EntryConnection;

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

        Schema::addGlobal($request, function ($schema) use ($request) {
            $schema->addRawField('relatedTo')
                ->type(\markhuot\CraftQL\Types\EntryConnection::singleton($schema->getRequest()))
                ->arguments([
                    'source' => Type::boolean(),
                    'target' => Type::boolean(),
                    'field' => Type::string(),
                    'sourceLocale' => Type::string(),
                ])
                ->resolve(function ($root, $args, $context, $info) use ($request) {
                    $criteria = \craft\elements\Entry::find();

                    $criteria = $criteria->relatedTo([
                        'element' => !@$args['source'] && !@$args['target'] ? $root['node']->id : null,
                        'sourceElement' => @$args['source'] == true ? $root['node']->id : null,
                        'targetElement' => @$args['target'] == true ? $root['node']->id : null,
                        'field' => @$args['field'] ?: null,
                        'sourceLocale' => @$args['sourceLocale'] ?: null,
                    ]);

                    unset($args['source']);
                    unset($args['target']);
                    unset($args['field']);
                    unset($args['sourceLocale']);

                    $criteria = $request->entries($criteria, $root, $args, $context, $info);
                    list($pageInfo, $entries) = \craft\helpers\Template::paginateCriteria($criteria);

                    return [
                        'totalCount' => $pageInfo->total,
                        'pageInfo' => $pageInfo,
                        'edges' => $entries,
                    ];
                });
        });

        $schema = new Schema($request);

        if ($token->can('query:entries') && $token->allowsMatch('/^query:entryType/')) {
            if (!empty($request->entryTypes()->all())) {
                $this->addEntriesSchema($schema);
            }
        }

        $config['fields'] = array_merge($config['fields'], $schema->config());

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

    /**
     * The fields you can query that return entries
     *
     * @return Schema
     */
    function addEntriesSchema($schema) {
        $schema->addRawField('entries')
            ->lists()
            ->type(Entry::interface($schema->getRequest()))
            ->arguments(Entry::args($schema->getRequest()))
            ->resolve(function ($root, $args, $context, $info) use ($schema) {
                return $schema->getRequest()->entries(\craft\elements\Entry::find(), $root, $args, $context, $info);
            });

        $schema->addRawField('entriesConnection')
            ->name('entriesConnection')
            ->type(EntryConnection::singleton($schema->getRequest()))
            ->arguments(Entry::args($schema->getRequest()))
            ->resolve(function ($root, $args, $context, $info) use ($schema) {
                $criteria = $schema->getRequest()->entries(\craft\elements\Entry::find(), $root, $args, $context, $info);
                list($pageInfo, $entries) = \craft\helpers\Template::paginateCriteria($criteria);

                return [
                    'totalCount' => $pageInfo->total,
                    'pageInfo' => $pageInfo,
                    'edges' => $entries,
                    'criteria' => $criteria,
                    'args' => $args,
                ];
            });

        $schema->addRawField('entry')
            ->type(Entry::interface($schema->getRequest()))
            ->arguments(Entry::args($schema->getRequest()))
            ->resolve(function ($root, $args, $context, $info) use ($schema) {
                return $schema->getRequest()->entries(\craft\elements\Entry::find(), $root, $args, $context, $info)->one();
            });

        $schema->addRawField('drafts')
            ->lists()
            ->type(EntryDraft::interface($schema->getRequest()))
            ->arguments([
                'id' => [
                    'type' => Type::nonNull(Type::int()),
                    'description' => 'The entry id to query for drafts'
                ],
            ])
            ->resolve(function ($root, $args) {
                return \Craft::$app->entryRevisions->getDraftsByEntryId($args['id']);
            });
    }

}