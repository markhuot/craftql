<?php

namespace markhuot\CraftQL\Factories;

use markhuot\CraftQL\Factories\BaseFactory;
use markhuot\CraftQL\Types\Entry;
use markhuot\CraftQL\Types\Section as SectionObjectType;

class Section extends BaseFactory {

    function make($raw, $request) {
        return new SectionObjectType($request, $raw);
    }

    function can($id, $mode='query') {
        $section = $this->repository->get($id);

        foreach ($this->request->entryTypes()->all() as $type) {
            if ($type->getContext()->sectionId == $id) {
                if ($this->request->token()->can("{$mode}:entryType:{$type->getContext()->id}")) {
                    return true;
                }
            }
        }

        return false;
    }

}