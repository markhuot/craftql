<?php

namespace markhuot\CraftQL;

class Request {

    private $token;
    private $entryTypes;
    private $volumes;
    private $categoryGroups;
    private $tagGroups;
    private $sections;
    private $globals;

    function __construct($token) {
        $this->token = $token;
    }

    function resolve($className, $params=[]) {
        return new $className($this, ...$params);
    }

    function addCategoryGroups(\markhuot\CraftQL\Factories\CategoryGroup $categoryGroups) {
        $this->categoryGroups = $categoryGroups;
    }

    function addTagGroups(\markhuot\CraftQL\Factories\TagGroup $tagGroups) {
        $this->tagGroups = $tagGroups;
    }

    function addEntryTypes(\markhuot\CraftQL\Factories\EntryType $entryTypes) {
        $this->entryTypes = $entryTypes;
    }

    function addVolumes(\markhuot\CraftQL\Factories\Volume $volumes) {
        $this->volumes = $volumes;
    }

    function addSections(\markhuot\CraftQL\Factories\Section $sections) {
        $this->sections = $sections;
    }

    function addGlobals(\markhuot\CraftQL\Factories\Globals $globals) {
        $this->globals = $globals;
    }

    function token() {
        return $this->token;
    }

    function categoryGroup($id) {
        return $this->categoryGroups->get($id);
    }

    function categoryGroups(): \markhuot\CraftQL\Factories\CategoryGroup {
        return $this->categoryGroups;
    }

    function tagGroup($id) {
        return $this->tagGroups->get($id);
    }

    function tagGroups(): \markhuot\CraftQL\Factories\TagGroup {
        return $this->tagGroups;
    }

    function entryTypes(): \markhuot\CraftQL\Factories\EntryType {
        return $this->entryTypes;
    }

    function volumes(): \markhuot\CraftQL\Factories\Volume {
        return $this->volumes;
    }

    function sections(): \markhuot\CraftQL\Factories\Section {
        return $this->sections;
    }

    function globals(): \markhuot\CraftQL\Factories\Globals {
        return $this->globals;
    }

    private function parseRelatedTo($relations, $id) {
        foreach ($relations as $index => &$relatedTo) {
            foreach (['element', 'sourceElement', 'targetElement'] as $key) {
                if (!empty($relatedTo["{$key}IsEdge"])) {
                    $relatedTo[$key] = $id;
                    unset($relatedTo["{$key}IsEdge"]);
                }
            }
        }

        return $relations;
    }

    function entries($criteria, $root, $args, $context, $info) {
        if (empty($args['section'])) {
            $args['sectionId'] = array_map(function ($value) {
                return $value->value;
            }, $this->sections()->enum()->getValues());
        }
        else {
            $args['sectionId'] = $args['section'];
            unset($args['section']);
        }

        if (empty($args['type'])) {
            $args['typeId'] = array_map(function ($value) {
                return $value->value;
            }, $this->entryTypes()->enum()->getValues());
        }
        else {
            $args['typeId'] = $args['type'];
            unset($args['type']);
        }

        if (!empty($args['relatedTo'])) {
            $criteria->relatedTo(array_merge(['and'], $this->parseRelatedTo($args['relatedTo'], @$root->id)));
            unset($args['relatedTo']);
        }

        if (!empty($args['orRelatedTo'])) {
            $criteria->relatedTo(array_merge(['or'], $this->parseRelatedTo($args['orRelatedTo'], @$root->id)));
            unset($args['orRelatedTo']);
        }

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

        if (!empty($info->fieldNodes)) {
            foreach ($info->fieldNodes[0]->selectionSet->selections as $selection) {
                if (isset($selection->name->value) && $selection->name->value == 'author') {
                    $criteria->with('author');
                }
            }
        }

        return $criteria;
    }

}
