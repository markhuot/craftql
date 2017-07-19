<?php

namespace markhuot\CraftQL\Repositories;

use Craft;

class Sections {

    private $sections = [];

    /**
     * Load sections out of the Craft database and convert them to
     * GraphQL sections
     *
     * @return void
     */
    function loadAllSections() {
        foreach (Craft::$app->sections->allSections as $section) {
            $this->sections[$section->handle] = $this->parseSectionToObject($section);
        }
    }

    /**
     * Get a loaded section. This will no pull sections out of the
     * database, so make sure you call `loadAllSections` first
     *
     * @param string $sectionHandle
     * @return void
     */
    function getSection($sectionHandle) {
        if (!isset($this->sections[$sectionHandle])) {
            $section = Craft::$app->sections->getSectionByHandle($sectionHandle);
            $this->sections[$sectionHandle] = $this->parseSectionToObject($section);
        }

        return $this->sections[$sectionHandle];
    }

    /**
     * Return the loaded sections, converted into GraphQL types
     *
     * @return array
     */
    function loadedSections() {
        return $this->sections;
    }

    /**
     * Convert a section from the Craft database into a native
     * GraphQL type
     *
     * @param [type] $section
     * @return void
     */
    function parseSectionToObject($section) {
        return \markhuot\CraftQL\GraphQL\Types\Section::make($section);
    }

}