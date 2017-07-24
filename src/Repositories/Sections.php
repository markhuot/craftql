<?php

namespace markhuot\CraftQL\Repositories;

use Craft;

class Sections {

    private $loaded = false;
    static $sections = [];

    /**
     * Load sections out of the Craft database and convert them to
     * GraphQL sections
     *
     * @return void
     */
    function loadAllSections() {
        if ($this->loaded) {
            return;
        }
        
        foreach (Craft::$app->sections->allSections as $section) {
            static::$sections[$section->handle] = $this->parseSectionToObject($section);
        }

        $this->loaded = true;
    }

    /**
     * Get a loaded section. This will no pull sections out of the
     * database, so make sure you call `loadAllSections` first
     *
     * @param string $sectionHandle
     * @return void
     */
    function getSection($sectionHandle) {
        if (!isset(static::$sections[$sectionHandle])) {
            $section = Craft::$app->sections->getSectionByHandle($sectionHandle);
            static::$sections[$sectionHandle] = $this->parseSectionToObject($section);
        }

        return static::$sections[$sectionHandle];
    }

    /**
     * Return the loaded sections, converted into GraphQL types
     *
     * @deprecated
     * @return array
     */
    function loadedSections() {
        return static::$sections;
    }

    /**
     * Return the loaded sections, converted into GraphQL types
     *
     * @return array
     */
    function getAllSections() {
        return static::$sections;
    }

    /**
     * Convert a section from the Craft database into a native
     * GraphQL type
     *
     * @param [type] $section
     * @return void
     */
    function parseSectionToObject($section) {
        if (isset(static::$sections[$section->handle])) {
            return static::$sections[$section->handle];
        }

        return static::$sections[$section->handle] = \markhuot\CraftQL\Types\Section::make($section);
    }

}