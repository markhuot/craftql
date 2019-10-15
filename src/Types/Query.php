<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\FieldBehaviors\AssetQueryArguments;
use Craft;
use markhuot\CraftQL\Builders\Schema;
use markhuot\CraftQL\FieldBehaviors\EntryQueryArguments;
use markhuot\CraftQL\FieldBehaviors\UserQueryArguments;
use markhuot\CraftQL\FieldBehaviors\CategoryQueryArguments;
use markhuot\CraftQL\FieldBehaviors\TagQueryArguments;
use markhuot\CraftQL\TypeModels\PageInfo;

class Query extends Schema {

    function boot() {
        $token = $this->request->token();

        $this->addStringField('helloWorld')
            ->resolve('Welcome to GraphQL! You now have a fully functional GraphQL endpoint.');

        $this->addStringField('ping')
            ->resolve('pong');

        if ($token->can('query:sites')) {
            $this->addSitesSchema();
        }

        if ($token->can('query:entries') && $token->allowsMatch('/^query:entryType/')) {
            $this->addEntriesSchema();
        }

        if ($token->can('query:assets')) {
            $this->addAssetsSchema();
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
            $this->addUsersSchema();
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
     * Adds sites to the schema
     */
    function addSitesSchema() {
        $field = $this->addField('sites')
            ->type(Site::class)
            ->lists()
            ->resolve(function ($root, $args) {
                if (!empty($args['handle'])) {
                    return [Craft::$app->sites->getSiteByHandle($args['handle'])];
                }

                if (!empty($args['id'])) {
                    return [Craft::$app->sites->getSiteById($args['id'])];
                }

                if (!empty($args['primary'])) {
                    return [Craft::$app->sites->getPrimarySite()];
                }

                return Craft::$app->sites->getAllSites();
            });

        $field->addStringArgument('handle');
        $field->addIntArgument('id');
        $field->addBooleanArgument('primary');
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
            ->use(new EntryQueryArguments)
            ->resolve(function ($root, $args, $context, $info) {
                return $this->getRequest()->entries(\craft\elements\Entry::find(), $root, $args, $context, $info)->all();
            });

         $this->addField('entriesConnection')
             ->name('entriesConnection')
             ->type(EntryConnection::class)
             ->use(new EntryQueryArguments)
             ->resolve(function ($root, $args, $context, $info) {
                 $criteria = $this->getRequest()->entries(\craft\elements\Entry::find(), $root, $args, $context, $info);
                 $totalCount = $criteria->count();
                 $offset = @$args['offset'] ?: 0;
                 $perPage = @$args['limit'] ?: 100;

                 return [
                     'totalCount' => $totalCount,
                     'pageInfo' => new PageInfo($offset, $perPage, $totalCount),
                     'edges' => $criteria->all(),
                     'criteria' => $criteria,
                     'args' => $args,
                 ];
             });

        $this->addField('entry')
            ->type(EntryInterface::class)
            ->use(new EntryQueryArguments)
            ->resolve(function ($root, $args, $context, $info) {
                return $this->getRequest()->entries(\craft\elements\Entry::find(), $root, $args, $context, $info)->one();
            });

        $draftField = $this->addField('draft')
            ->type(EntryInterface::class)
            ->use(new EntryQueryArguments)
            ->resolve(function ($root, $args, $context, $info) {
                return Craft::$app->entryRevisions->getDraftById($args['draftId']);
            });

        $draftField->addIntArgument('draftId')->nonNull();
    }

    /**
     * The fields you can query that return assets
     */
    function addAssetsSchema() {
        if ($this->getRequest()->volumes()->count() == 0) {
            return;
        }

        $this->addField('assets')
            ->type(VolumeInterface::class)
            ->use(new AssetQueryArguments)
            ->lists()
            ->resolve(function ($root, $args) {
                $criteria = \craft\elements\Asset::find();

                foreach ($args as $key => $value) {
                    $criteria = $criteria->{$key}($value);
                }

                return $criteria->all();
            });
    }

    /**
     * The fields you can query that return globals
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
                ->arguments(function ($field) {
                    $field->addStringArgument('site');
                    $field->addIntArgument('siteId');
                })
                ->resolve(function ($root, $args) {
                    if (!empty($args['site'])) {
                        $siteId = Craft::$app->getSites()->getSiteByHandle($args['site'])->id;
                    }
                    else if (!empty($args['siteId'])) {
                        $siteId = $args['siteId'];
                    }
                    else {
                        $siteId = Craft::$app->getSites()->getCurrentSite()->id;
                    }

                    $sets = [];
                    $setIds = \Craft::$app->globals->getAllSetIds();

                    foreach ($setIds as $id) {
                        $set = \Craft::$app->globals->getSetById($id, $siteId);
                        $sets[$set->handle] = $set;
                    }

                    return $sets;
                });
        }
    }

    /**
     * The fields you can query that return tags
     */
    function addTagsSchema() {
        if ($this->request->tagGroups()->count() == 0) {
            return;
        }

        $this->addField('tags')
            ->lists()
            ->type(TagInterface::class)
            ->use(new TagQueryArguments)
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
            ->use(new TagQueryArguments)
            ->resolve(function ($root, $args, $context, $info) {
                $criteria = \craft\elements\Tag::find();
                $totalCount = $criteria->count();
                $offset = @$args['offset'] ?: 0;
                $perPage = @$args['limit'] ?: 100;

                if (isset($args['group'])) {
                    $args['groupId'] = $args['group'];
                    unset($args['group']);
                }

                foreach ($args as $key => $value) {
                    $criteria = $criteria->{$key}($value);
                }

                return [
                    'totalCount' => $totalCount,
                    'pageInfo' => new PageInfo($offset, $perPage, $totalCount),
                    'edges' => $criteria->all(),
                    'criteria' => $criteria,
                    'args' => $args,
                ];
            });
    }

    /**
     * The fields you can query that return categories
     */
    function addCategoriesSchema() {
        if ($this->request->categoryGroups()->count() == 0) {
            return;
        }

        $categoryResolver = function ($root, $args) {
            $criteria = \craft\elements\Category::find();

            if (isset($args['group'])) {
                $args['groupId'] = $args['group'];
                unset($args['group']);
            }

            foreach ($args as $key => $value) {
                $criteria = $criteria->{$key}($value);
            }

            return $criteria;
        };

        $this->addField('categories')
            ->lists()
            ->type(CategoryInterface::class)
            ->use(new CategoryQueryArguments)
            ->resolve(function ($root, $args) use ($categoryResolver) {
                return $categoryResolver($root, $args)->all();
            });

        $this->addField('category')
            ->type(CategoryInterface::class)
            ->use(new CategoryQueryArguments)
            ->resolve(function ($root, $args) use ($categoryResolver) {
                return $categoryResolver($root, $args)->one();
            });

        $this->addField('categoriesConnection')
            ->type(CategoryConnection::class)
            ->use(new CategoryQueryArguments)
            ->resolve(function ($root, $args) use ($categoryResolver) {
                $criteria = $categoryResolver($root, $args);
                $totalCount = $criteria->count();
                $offset = @$args['offset'] ?: 0;
                $perPage = @$args['limit'] ?: 100;

                return [
                    'totalCount' => $totalCount,
                    'pageInfo' => new PageInfo($offset, $perPage, $totalCount),
                    'edges' => $criteria->all(),
                ];
            });
    }

    function addUsersSchema() {
        $userResolver = function ($root, $args) {
            $criteria = \craft\elements\User::find();

            foreach ($args as $key => $value) {
                $criteria = $criteria->{$key}($value);
            }

            return $criteria;
        };

        $this->addField('users')
            ->lists()
            ->type(User::class)
            ->use(new UserQueryArguments)
            ->resolve(function ($root, $args) use ($userResolver) {
                return $userResolver($root, $args)->all();
            });

        $this->addField('user')
            ->type(User::class)
            ->use(new UserQueryArguments)
            ->resolve(function ($root, $args) use ($userResolver) {
                return $userResolver($root, $args)->one();
            });
    }

}
