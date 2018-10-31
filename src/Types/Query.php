<?php

namespace markhuot\CraftQL\Types;

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

    function getSections() {
        return \Craft::$app->sections->getAllSections();
    }

    function getSites($root, $args, $context, $info) {
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

    function getDraft($root, $args, $context, $info) {
        return Craft::$app->entryRevisions->getDraftById($args['draftId']);
    }

    function getAssets($root, $args) {
        $criteria = \craft\elements\Asset::find();

        foreach ($args as $key => $value) {
            $criteria = $criteria->{$key}($value);
        }

        return $criteria->all();
    }

    function getGlobals($root, $args, $context, $info) {
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

    function getTags($root, $args, $context, $info) {
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

    function getTagsConnection($root, $args, $context, $info) {
        $criteria = \craft\elements\Tag::find();

        if (isset($args['group'])) {
            $args['groupId'] = $args['group'];
            unset($args['group']);
        }

        foreach ($args as $key => $value) {
            $criteria = $criteria->{$key}($value);
        }

        list($pageInfo, $tags) = \craft\helpers\Template::paginateCriteria($criteria);
        $pageInfo->limit = @$args['limit'] ?: 100;

        return [
            'totalCount' => $pageInfo->total,
            'pageInfo' => $pageInfo,
            'edges' => $tags,
            'criteria' => $criteria,
            'args' => $args,
        ];
    }

    function getCategories($root, $args) {
        return $this->getCategoryCriteria($root, $args)->all();
    }

    function getCategory($root, $args) {
        return $this->getCategoryCriteria($root, $args)->one();
    }

    function getCategoriesConnection($root, $args) {
        list($pageInfo, $categories) = \craft\helpers\Template::paginateCriteria($this->getCategoryCriteria($root, $args));
        $pageInfo->limit = @$args['limit'] ?: 100;

        return [
            'totalCount' => $pageInfo->total,
            'pageInfo' => $pageInfo,
            'edges' => $categories,
        ];
    }

    protected function getCategoryCriteria($root, $args) {
        $criteria = \craft\elements\Category::find();

        if (isset($args['group'])) {
            $args['groupId'] = $args['group'];
            unset($args['group']);
        }

        foreach ($args as $key => $value) {
            $criteria = $criteria->{$key}($value);
        }

        return $criteria;
    }

    protected function getUserCriteria($root, $args) {
        $criteria = \craft\elements\User::find();

        foreach ($args as $key => $value) {
            $criteria = $criteria->{$key}($value);
        }

        return $criteria;
    }

    function getUsers($root, $args) {
        return $this->getUserCriteria($root, $args)->all();
    }

    function getUser($root, $args) {
        return $this->getUserCriteria($root, $args)->first();
    }

    function getEntriesConnection($root, $args, $context, $info) {
        $criteria = $this->getRequest()->entries(\craft\elements\Entry::find(), $root, $args, $context, $info);
        list($pageInfo, $entries) = \craft\helpers\Template::paginateCriteria($criteria);

        return [
            'totalCount' => $pageInfo->total,
            'pageInfo' => new PageInfo($pageInfo, @$args['limit']),
            'edges' => $entries,
            'criteria' => $criteria,
            'args' => $args,
        ];
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

        if ($token->can('query:users')) {
            $this->addUsersSchema();
        }

        if ($token->can('query:sections')) {
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

    function getEntries($root, $args, $context, $info) {
        return $this->getRequest()->entries(\craft\elements\Entry::find(), $root, $args, $context, $info)->all();
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
            ->use(new EntryQueryArguments);

        $this->addField('entriesConnection')
            ->name('entriesConnection')
            ->type(EntryConnection::class)
            ->use(new EntryQueryArguments);

        $this->addField('entry')
            ->type(EntryInterface::class)
            ->use(new EntryQueryArguments)
            ->resolve(function ($root, $args, $context, $info) {
                return $this->getRequest()->entries(\craft\elements\Entry::find(), $root, $args, $context, $info)->one();
            });

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

        if ($this->request->globals()->count() > 0) {
            $this->addField('globals')
                ->type(\markhuot\CraftQL\Types\GlobalsSet::class)
                ->arguments(function ($field) {
                    $field->addStringArgument('site');
                    $field->addIntArgument('siteId');
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
            ->use(new TagQueryArguments);

        $this->addField('tagsConnection')
            ->type(TagConnection::class)
            ->use(new TagQueryArguments);
    }

    /**
     * The fields you can query that return categories
     */
    function addCategoriesSchema() {
        if ($this->request->categoryGroups()->count() == 0) {
            return;
        }

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