<?php

namespace markhuot\CraftQL\Factories;

use markhuot\CraftQL\Factories\BaseFactory;
use markhuot\CraftQL\Types\Section as SectionObjectType;

class Section extends BaseFactory {

    function make($raw, $request) {
        return new SectionObjectType($raw, $request);
    }

    function can($id) {
        $section = $this->repository->get($id);
        foreach ($section->entryTypes as $type) {
            if ($this->request->token()->canNot("query:entryType:{$type->id}")) {
                return false;
            }
        }

        return true;
    }

    function enumValueName($object) {
        return preg_replace('/Section$/', '', $object->name);
    }

}