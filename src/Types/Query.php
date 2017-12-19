<?php

namespace markhuot\CraftQL\Types;

use yii\base\Component;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Craft;
use markhuot\CraftQL\Builders\Schema;
use markhuot\CraftQL\Request;

class Query extends ObjectType {

    function __construct(Request $request) {
        $token = $request->token();

        $config = [
            'name' => 'Query',
            'fields' => [],
        ];

        $schema = new Schema($request);

        $schema->addStringField('helloWorld')
            ->resolve('Welcome to GraphQL! You now have a fully functional GraphQL endpoint.');

        if ($token->can('query:entries') && $token->allowsMatch('/^query:entryType/')) {
            if (!empty($request->entryTypes()->all())) {
                $this->addEntriesSchema($schema);
            }
        }

        if ($token->can('query:globals')) {
            $this->addGlobalsSchema($schema);
        }

        if ($token->can('query:tags')) {
            $this->addTagsSchema($schema);
        }

        if ($token->can('query:categories')) {
            $this->addCategoriesSchema($schema);
        }

        if ($token->can('query:users')) {
            $schema->addField('users')
                ->lists()
                ->type(User::class)
                ->arguments([
                    'admin' => Type::boolean(),
                    'email' => Type::string(),
                    'firstName' => Type::string(),
                    'group' => Type::string(),
                    'groupId' => Type::string(),
                    'id' => Type::int(),
                    'lastLoginDate' => Type::int(),
                    'lastName' => Type::string(),
                    'limit' => Type::int(),
                    'offset' => Type::int(),
                    'order' => Type::string(),
                    'search' => Type::string(),
                    // 'status' => static::statusEnum(),
                    'username' => Type::string(),
                ])
                ->resolve(function ($root, $args, $context, $info) {
                    $criteria = \craft\elements\User::find();

                    foreach ($args as $key => $value) {
                        $criteria = $criteria->{$key}($value);
                    }

                    return $criteria->all();
                });
        }

        if ($token->can('query:sections')) {
            $schema->addField('sections')
                ->lists()
                ->type(Section::class)
                ->resolve(function ($root, $args, $context, $info) {
                    return \Craft::$app->sections->allSections;
                });
        }

        $config['fields'] = array_merge($config['fields'], $schema->getFieldConfig());

        parent::__construct($config);
    }

    /**
     * The fields you can query that return entries
     *
     * @return Schema
     */
    function addEntriesSchema($schema) {
        $schema->addField('entries')
            ->lists()
            ->type(EntryInterface::class)
            ->arguments(Entry::args($schema->getRequest()))
            ->resolve(function ($root, $args, $context, $info) use ($schema) {
                return $schema->getRequest()->entries(\craft\elements\Entry::find(), $root, $args, $context, $info);
            });

         $schema->addField('entriesConnection')
             ->name('entriesConnection')
             ->type(EntryConnection::class)
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

        $schema->addField('entry')
            ->type(EntryInterface::class)
            ->arguments(Entry::args($schema->getRequest()))
            ->resolve(function ($root, $args, $context, $info) use ($schema) {
                return $schema->getRequest()->entries(\craft\elements\Entry::find(), $root, $args, $context, $info)->one();
            });
    }

    /**
     * The fields you can query that return globals
     *
     * @return Schema
     */
    function addGlobalsSchema($schema) {

        // $schema->addObjectField('globals')
        //     ->config(function ($object) use ($request) {
        //         $object->name('GlobalSet');
        //         foreach ($request->globals()->all() as $globalSet) {
        //             $object->addField($globalSet->getContext()->handle)
        //                 ->type($globalSet);
        //         }
        //     })
        //     ->resolve(function ($root, $args) {
        //         $sets = [];
        //         foreach (\Craft::$app->globals->allSets as $set) {
        //             $sets[$set->handle] = $set;
        //         }
        //         return $sets;
        //     });

        $schema->addField('globals')
            ->type(\markhuot\CraftQL\Types\GlobalsSet::class)
            ->resolve(function ($root, $args) {
                $sets = [];
                foreach (\Craft::$app->globals->allSets as $set) {
                    $sets[$set->handle] = $set;
                }
                return $sets;
            });
    }

    /**
     * The fields you can query that return tags
     *
     * @return Schema
     */
    function addTagsSchema($schema) {
        $schema->addField('tags')
            ->lists()
            ->type(TagInterface::class)
            ->arguments(Tag::args($schema->getRequest()))
            ->resolve(function ($root, $args, $context, $info) {
                $criteria = \craft\elements\Tag::find();

                if (isset($args['group'])) {
                    $args['groupId'] = $args['group'];
                    unset($args['group']);
                }

                foreach ($args as $key => $value) {
                    $criteria = $criteria->{$key}($value);
                }

                return $criteria->all();
            });

        $schema->addField('tagsConnection')
            ->type(TagConnection::class)
            ->arguments(Tag::args($schema->getRequest()))
            ->resolve(function ($root, $args, $context, $info) {
                $criteria = \craft\elements\Tag::find();

                if (isset($args['group'])) {
                    $args['groupId'] = $args['group'];
                    unset($args['group']);
                }

                foreach ($args as $key => $value) {
                    $criteria = $criteria->{$key}($value);
                }

                list($pageInfo, $tags) = \craft\helpers\Template::paginateCriteria($criteria);

                return [
                    'totalCount' => $pageInfo->total,
                    'pageInfo' => $pageInfo,
                    'edges' => $tags,
                    'criteria' => $criteria,
                    'args' => $args,
                ];
            });
    }

    /**
     * The fields you can query that return categories
     *
     * @return Schema
     */
    function addCategoriesSchema($schema) {
        $schema->addField('categories')
            ->lists()
            ->type(CategoryInterface::class)
            ->arguments(Category::args($schema->getRequest()))
            ->resolve(function ($root, $args) {
                $criteria = \craft\elements\Category::find();

                if (isset($args['group'])) {
                    $args['groupId'] = $args['group'];
                    unset($args['group']);
                }

                foreach ($args as $key => $value) {
                    $criteria = $criteria->{$key}($value);
                }

                return $criteria->all();
            });

        $schema->addField('categoriesConnection')
            ->type(CategoryConnection::class)
            ->arguments(Category::args($schema->getRequest()))
            ->resolve(function ($root, $args) {
                $criteria = \craft\elements\Category::find();

                if (isset($args['group'])) {
                    $args['groupId'] = $args['group'];
                    unset($args['group']);
                }

                foreach ($args as $key => $value) {
                    $criteria = $criteria->{$key}($value);
                }

                list($pageInfo, $categories) = \craft\helpers\Template::paginateCriteria($criteria);

                return [
                    'totalCount' => $pageInfo->total,
                    'pageInfo' => $pageInfo,
                    'edges' => $categories,
                    'criteria' => $criteria,
                    'args' => $args,
                ];
            });
    }

}