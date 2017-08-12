<?php

namespace markhuot\CraftQL;

class Request {

    private $token;
    private $entryTypes;
    private $volumes;
    private $categoryGroups;

    function __construct($token) {
        $this->token = $token;
    }

    function addCategoryGroups($categoryGroups) {
        $this->categoryGroups = $categoryGroups;
    }

    function addEntryTypes($entryTypes) {
        $this->entryTypes = $entryTypes;
    }

    function addVolumes($volumes) {
        $this->volumes = $volumes;
    }

    function token() {
        return $this->token;
    }

    function categoryGroup($id) {
        return $this->categoryGroups->get($id);
    }

    function entryTypes() {
        return $this->entryTypes;
    }

    function volumes() {
        return $this->volumes;
    }

}