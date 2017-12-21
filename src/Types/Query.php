<?php

namespace markhuot\CraftQL\Types;

use yii\base\Component;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Craft;
use markhuot\CraftQL\Builders\Schema;
use markhuot\CraftQL\Request;
use markhuot\CraftQL\FieldBehaviors\EntryQueryArguments;

class Query extends Schema {

    function boot() {
        $token = $this->request->token();

        $this->addStringField('helloWorld')
            ->resolve('Welcome to GraphQL! You now have a fully functional GraphQL endpoint.');

        if ($token->can('query:entries') && $token->allowsMatch('/^query:entryType/')) {
            $this->addEntriesSchema();
        }

        if ($token->can('query:globals')) {
            $this->addGlobalsSchema();
        }

        if ($token->can('query:tags')) {
            $this->addTagsSchema();
        }

        if ($token->can('query:categories')) {
            $this->addCategoriesSchema();
        }

        if ($token->can('query:users')) {
            $this->addField('users')
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
            $this->addField('sections')
                ->lists()
                ->type(Section::class)
                ->resolve(function ($root, $args, $context, $info) {
                    return \Craft::$app->sections->allSections;
                });
        }
    }

    /**
     * The fields you can query that return entries
     *
     * @return Schema
     */
    function addEntriesSchema() {
        if ($this->request->entryTypes()->count() == 0) {
            return;
        }

        $this->addField('entries')
            ->lists()
            ->type(EntryInterface::class)
            ->use(EntryQueryArguments::class)
            ->resolve(function ($root, $args, $context, $info) {
                return $this->getRequest()->entries(\craft\elements\Entry::find(), $root, $args, $context, $info);
            });

         $this->addField('entriesConnection')
             ->name('entriesConnection')
             ->type(EntryConnection::class)
             ->use(EntryQueryArguments::class)
             ->resolve(function ($root, $args, $context, $info) {
                 $criteria = $this->getRequest()->entries(\craft\elements\Entry::find(), $root, $args, $context, $info);
                 list($pageInfo, $entries) = \craft\helpers\Template::paginateCriteria($criteria);

                 return [
                     'totalCount' => $pageInfo->total,
                     'pageInfo' => $pageInfo,
                     'edges' => $entries,
                     'criteria' => $criteria,
                     'args' => $args,
                 ];
             });

        $this->addField('entry')
            ->type(EntryInterface::class)
            ->use(EntryQueryArguments::class)
            ->resolve(function ($root, $args, $context, $info) {
                return $this->getRequest()->entries(\craft\elements\Entry::find(), $root, $args, $context, $info)->one();
            });
    }

    /**
     * The fields you can query that return globals
     *
     * @return Schema
     */
    function addGlobalsSchema() {

        // $this->addObjectField('globals')
        //     ->config(function ($object) use ($this->request) {
        //         $object->name('GlobalSet');
        //         foreach ($this->request->globals()->all() as $globalSet) {
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

        if ($this->request->globals()->count() > 0) {
            $this->addField('globals')
                ->type(\markhuot\CraftQL\Types\GlobalsSet::class)
                ->resolve(function ($root, $args) {
                    $sets = [];
                    foreach (\Craft::$app->globals->allSets as $set) {
                        $sets[$set->handle] = $set;
                    }
                    return $sets;
                });
        }
    }

    /**
     * The fields you can query that return tags
     *
     * @return Schema
     */
    function addTagsSchema() {
        if ($this->request->tagGroups()->count() == 0) {
            return;
        }

        $this->addField('tags')
            ->lists()
            ->type(TagInterface::class)
            ->arguments(Tag::args($this->getRequest()))
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

        $this->addField('tagsConnection')
            ->type(TagConnection::class)
            ->arguments(Tag::args($this->getRequest()))
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
    function addCategoriesSchema() {
        if ($this->request->categoryGroups()->count() == 0) {
            return;
        }

        $this->addField('categories')
            ->lists()
            ->type(CategoryInterface::class)
            ->arguments(Category::args($this->getRequest()))
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

        $this->addField('categoriesConnection')
            ->type(CategoryConnection::class)
            ->arguments(Category::args($this->getRequest()))
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