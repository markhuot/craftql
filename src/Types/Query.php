<?php

namespace markhuot\CraftQL\Types;

use craft\elements\db\ElementQueryInterface;
use craft\elements\Entry;
use Craft;
use markhuot\CraftQL\Arguments\EntryQueryArguments;
use markhuot\CraftQL\Builders\Schema;

class Query {

    public $helloWorld = 'Welcome to GraphQL! You now have a fully functional GraphQL endpoint.';
    public $ping = 'pong';

    /**
     * @return Site[]
     */
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

    /**
     * @return EntryInterface[]
     */
    function getCraftQLEntries($request, $root, $args, $context, $info) {
        return static::getEntriesCriteria($args)->all();
    }

    /**
     * @return EntryInterface
     */
    function getCraftQLEntry($request, $root, EntryQueryArguments $args, $context, $info) {
        return static::getEntriesCriteria($args)->one();
    }

    /**
     * @return Section[]
     */
    function getCraftQLSections() {
        return \Craft::$app->sections->getAllSections();
    }

    /**
     * @return EntryInterface
     */
    function getCraftQLDraft($request, $root, $args, $context, $info) {
        return Craft::$app->entryRevisions->getDraftById($args['draftId']);
    }

    /**
     * @return VolumeInterface[]
     */
    function getCraftQLAssets($request, $root, $args) {
        $criteria = \craft\elements\Asset::find();

        foreach ($args as $key => $value) {
            $criteria = $criteria->{$key}($value);
        }

        return $criteria->all();
    }

    /**
     * @return GlobalSets
     */
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

    /**
     * @return TagInterface[]
     */
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

    /**
     * @return TagConnection
     */
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

    /**
     * @return CategoryInterface[]
     */
    function getCraftQLCategories($request, $root, $args) {
        return static::getCategoryCriteria($args)->all();
    }

    /**
     * @return CategoryInterface
     */
    function getCraftQLCategory($request, $root, $args) {
        return static::getCategoryCriteria($args)->one();
    }

    /**
     * @return CategoryConnection
     */
    function getCraftQLCategoriesConnection($request, $root, $args) {
        $criteria = static::getCategoryCriteria($args);
        list($pageInfo, $categories) = \craft\helpers\Template::paginateCriteria($criteria);
        return new CategoryConnection(new PageInfo($pageInfo, @$args['limit']), $categories);
    }

    /**
     * @return ElementQueryInterface
     */
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

    /**
     * @return ElementQueryInterface
     */
    protected function getUserCriteria($request, $root, $args) {
        $criteria = \craft\elements\User::find();

        foreach ($args as $key => $value) {
            $criteria = $criteria->{$key}($value);
        }

        return $criteria;
    }

    /**
     * @return User[]
     */
    function getCraftQLUsers($request, $root, $args) {
        return $this->getUserCriteria($request, $root, $args)->all();
    }

    /**
     * @return User
     */
    function getCraftQLUser($request, $root, $args) {
        return $this->getUserCriteria($request, $root, $args)->first();
    }

    /**
     * @return EntryConnection
     */
    function getCraftQLEntriesConnection($request, $root, $args, $context, $info) {
        $criteria = static::getEntriesCriteria($args);
        list($pageInfo, $entries) = \craft\helpers\Template::paginateCriteria($criteria);
        return new EntryConnection(new PageInfo($pageInfo, @$args['limit']), $entries);
    }

    /**
     * @return ElementQueryInterface
     */
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

        // if (!empty($args['idNot'])) {
        //     // this looks a little unusual to fit craft\helpers\Db::parseParam
        //     $criteria->id('and, !='.implode(', !=', $args['idNot']));
        //     unset($args['idNot']);
        // }

        // var_dump($args);
        // die;

        foreach ($args as $key => $value) {
            if ($value !== null) {
                $criteria = $criteria->{$key}($value);
            }
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

}