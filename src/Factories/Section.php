<?php

namespace markhuot\CraftQL\Factories;

use markhuot\CraftQL\Factories\BaseFactory;
use markhuot\CraftQL\Types\Section as SectionObjectType;

class Section extends BaseFactory {

    function make($raw, $request) {
        return new SectionObjectType($raw, $request);
    }

    function can($id, $mode='query') {
        $section = $this->repository->get($id);
        foreach ($section->entryTypes as $type) {
            if ($this->request->token()->can("{$mode}:entryType:{$type->id}")) {
                return true;
            }
        }

        return false;
    }

}