<?php

namespace markhuot\CraftQL\Types;

use craft\elements\Entry;
use markhuot\CraftQL\CraftQL;
use markhuot\CraftQL\FieldBehaviors\AssetQueryArguments;
use yii\base\Component;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Craft;
use markhuot\CraftQL\Builders\Schema;
use markhuot\CraftQL\Request;
use markhuot\CraftQL\FieldBehaviors\EntryQueryArguments;
use markhuot\CraftQL\FieldBehaviors\UserQueryArguments;
use markhuot\CraftQL\FieldBehaviors\CategoryQueryArguments;
use markhuot\CraftQL\FieldBehaviors\TagQueryArguments;

class Query extends Schema {

    public $helloWorld = 'Welcome to GraphQL! You now have a fully functional GraphQL endpoint.';
    public $ping = 'pong';

    function getCraftQLEntries($request, $root, $args, $context, $info) {
        return static::getEntriesCriteria($args)->all();
    }

    function getCraftQLEntry($request, $root, $args, $context, $info) {
        return static::getEntriesCriteria($args)->one();
    }

    function getCraftQLSections() {
        return \Craft::$app->sections->getAllSections();
    }

    function getCraftQLSites($request, $root, $args, $context, $info) {
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
    }

    function getCraftQLDraft($request, $root, $args, $context, $info) {
        return Craft::$app->entryRevisions->getDraftById($args['draftId']);
    }

    function getCraftQLAssets($request, $root, $args) {
        $criteria = \craft\elements\Asset::find();

        foreach ($args as $key => $value) {
            $criteria = $criteria->{$key}($value);
        }

        return $criteria->all();
    }

    function getCraftQLGlobals($request, $root, $args, $context, $info) {
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
    }

    function getCraftQLTags($request, $root, $args, $context, $info) {
        $criteria = \craft\elements\Tag::find();

        if (isset($args['group'])) {
            $args['groupId'] = $args['group'];
            unset($args['group']);
        }

        foreach ($args as $key => $value) {
            $criteria = $criteria->{$key}($value);
        }

        return $criteria->all();
    }

    function getCraftQLTagsConnection($request, $root, $args, $context, $info) {
        $criteria = \craft\elements\Tag::find();

        if (isset($args['group'])) {
            $args['groupId'] = $args['group'];
            unset($args['group']);
        }

        foreach ($args as $key => $value) {
            $criteria = $criteria->{$key}($value);
        }

        list($pageInfo, $tags) = \craft\helpers\Template::paginateCriteria($criteria);
        $pageInfo = new PageInfo($pageInfo, @$args['limit'] ?: 100);
        return new TagConnection($pageInfo, $tags);
    }

    function getCraftQLCategories($request, $root, $args) {
        return static::getCategoryCriteria($args)->all();
    }

    function getCraftQLCategory($request, $root, $args) {
        return static::getCategoryCriteria($args)->one();
    }

    function getCraftQLCategoriesConnection($request, $root, $args) {
        $criteria = static::getCategoryCriteria($args);
        list($pageInfo, $categories) = \craft\helpers\Template::paginateCriteria($criteria);
        return new CategoryConnection(new PageInfo($pageInfo, @$args['limit']), $categories);
    }

    static function getCategoryCriteria($args, $criteria=null) {
        if (empty($criteria)) {
            $criteria = \craft\elements\Category::find();
        }

        if (isset($args['group'])) {
            $args['groupId'] = $args['group'];
            unset($args['group']);
        }

        foreach ($args as $key => $value) {
            $criteria = $criteria->{$key}($value);
        }

        return $criteria;
    }

    protected function getCraftQLUserCriteria($request, $root, $args) {
        $criteria = \craft\elements\User::find();

        foreach ($args as $key => $value) {
            $criteria = $criteria->{$key}($value);
        }

        return $criteria;
    }

    protected function getUserCriteria($request, $root, $args) {
        $criteria = \craft\elements\User::find();

        foreach ($args as $key => $value) {
            $criteria = $criteria->{$key}($value);
        }

        return $criteria;
    }

    function getCraftQLUsers($request, $root, $args) {
        return $this->getUserCriteria($request, $root, $args)->all();
    }

    function getCraftQLUser($request, $root, $args) {
        return $this->getUserCriteria($request, $root, $args)->first();
    }

    function getCraftQLEntriesConnection($request, $root, $args, $context, $info) {
        $criteria = static::getEntriesCriteria($args);
        list($pageInfo, $entries) = \craft\helpers\Template::paginateCriteria($criteria);
        return new EntryConnection(new PageInfo($pageInfo, @$args['limit']), $entries);
    }

    static function getEntriesCriteria($args, $criteria=null) {
        if (empty($criteria)) {
            $criteria = Entry::find();
        }

        // @TODO, need access to the request that we don't have
        // if (empty($args['section'])) {
        //     $args['sectionId'] = array_map(function ($value) {
        //         return $value->value;
        //     }, $this->sections()->enum()->getValues());
        // }
        // else {
        //     $args['sectionId'] = $args['section'];
        //     unset($args['section']);
        // }

        // @TODO, need access to the request that we don't have
        // if (empty($args['type'])) {
        //     $args['typeId'] = array_map(function ($value) {
        //         return $value->value;
        //     }, $this->entryTypes()->enum()->getValues());
        // }
        // else {
        //     $args['typeId'] = $args['type'];
        //     unset($args['type']);
        // }

        // if (!empty($args['relatedTo'])) {
        //     $criteria->relatedTo(array_merge(['and'], $this->parseRelatedTo($args['relatedTo'], @$root['node']->id)));
        //     unset($args['relatedTo']);
        // }

        // if (!empty($args['orRelatedTo'])) {
        //     $criteria->relatedTo(array_merge(['or'], $this->parseRelatedTo($args['orRelatedTo'], @$root['node']->id)));
        //     unset($args['orRelatedTo']);
        // }

        if (!empty($args['idNot'])) {
            // this looks a little unusual to fit craft\helpers\Db::parseParam
            $criteria->id('and, !='.implode(', !=', $args['idNot']));
            unset($args['idNot']);
        }

        // var_dump($args);
        // die;

        foreach ($args as $key => $value) {
            $criteria = $criteria->{$key}($value);
        }

        // if (!empty($info->fieldNodes)) {
        //     foreach ($info->fieldNodes[0]->selectionSet->selections as $selection) {
        //         if (isset($selection->name->value) && $selection->name->value == 'author') {
        //             $criteria->with('author');
        //         }
        //     }
        // }

        return $criteria;
    }

    function boot() {
        $token = $this->request->token();

        $this->addStringField('helloWorld');

        $this->addStringField('ping');

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

        if (false && $token->can('query:users')) {
            $this->addUsersSchema();
        }

        if (false && $token->can('query:sections')) {
            $this->addField('sections')
                ->lists()
                ->type(Section::class);
        }
    }

    /**
     * Adds sites to the schema
     */
    function addSitesSchema() {
        $field = $this->addField('sites')
            ->type(Site::class)
            ->lists();

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
        // @TODO implement this even though we're probably getting rid of the factories
        // if ($this->request->entryTypes()->count() == 0) {
        //     return;
        // }

        $this->addField('entries')
            ->lists()
            ->type(EntryInterface::class)
            ->use(new EntryQueryArguments);

        $this->addField('entriesConnection')
            ->type(EntryConnection::class)
            ->use(new EntryQueryArguments);

        $this->addField('entry')
            ->type(EntryInterface::class)
            ->use(new EntryQueryArguments);

        $draftField = $this->addField('draft')
            ->type(EntryInterface::class)
            ->use(new EntryQueryArguments);

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
            ->lists();
    }

    /**
     * The fields you can query that return globals
     */
    function addGlobalsSchema() {
        // if ($this->request->globals()->count() > 0) {
            $this->addField('globals')
                ->type(GlobalSets::class)
                // ->arguments(function ($field) {
                //     $field->addStringArgument('site');
                //     $field->addIntArgument('siteId');
                // })
            ;
        // }
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
            ->use(new TagQueryArguments);

        $this->addField('tagsConnection')
            ->type(TagConnection::class)
            ->use(new TagQueryArguments);
    }

    /**
     * The fields you can query that return categories
     */
    function addCategoriesSchema() {
        // @TODO implement this even though we're probably getting rid of the factories
        // if ($this->request->categoryGroups()->count() == 0) {
        //     return;
        // }

        $this->addField('categories')
            ->lists()
            ->type(CategoryInterface::class)
            ->use(new CategoryQueryArguments);

        $this->addField('category')
            ->type(CategoryInterface::class)
            ->use(new CategoryQueryArguments);

        $this->addField('categoriesConnection')
            ->type(CategoryConnection::class)
            ->use(new CategoryQueryArguments);
    }

    function addUsersSchema() {
        $this->addField('users')
            ->lists()
            ->type(User::class)
            ->use(new UserQueryArguments);

        $this->addField('user')
            ->type(User::class)
            ->use(new UserQueryArguments);
    }

}