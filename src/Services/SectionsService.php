<?php

namespace markhuot\CraftQL\Services;

use Craft;
use markhuot\CraftQL\Models\Token;

class SectionsService {

    private $sections = [];

    function __construct() {
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
    }

    function getById($id) {
        return $this->sections[$id];
    }

}