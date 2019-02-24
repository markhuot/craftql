<?php

namespace markhuot\CraftQL\Repositories;

use Craft;
use markhuot\CraftQL\Types\Query;

class Section {

    private $sections = [];

    function load() {
        $sections = (new \craft\db\Query())
            ->select([
                'sections.id',
                'sections.structureId',
                'sections.name',
                'sections.handle',
                'sections.type',
                'sections.enableVersioning',
                'sections.propagateEntries',
                'structures.maxLevels',
            ])
            ->leftJoin('{{%structures}} structures', '[[structures.id]] = [[sections.structureId]]')
            ->from(['{{%sections}} sections'])
            ->orderBy(['name' => SORT_ASC])
            ->all();

        foreach ($sections as $section) {
            $this->sections[$section['id']] = $section;
        }

        // foreach (Craft::$app->sections->allSections as $section) {
        //     $this->sections[$section->id] = $section;
        //     if (!empty($section->uid)) {
        //         $this->sections[$section->uid] = $section;
        //     }
        // }
    }
    
    function get($id) {
        $section = new \craft\models\Section($this->sections[$id]);

        return $section;
    }

    function all() {
        return array_map(function ($section) {
            return $this->get($section['id']);
        }, $this->sections);
    }

}