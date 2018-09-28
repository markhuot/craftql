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
//        \Yii::beginProfile('sectionCan::'.$id, 'sectionCan::'.$id);
//        xdebug_start_trace('/Users/markhuot/Desktop/craftql');

        $return = false;
        $section = $this->repository->get($id);

        foreach ($this->request->entryTypes()->all() as $type) {
            if ($type->getContext()->sectionId == $id) {
                if ($this->request->token()->can("{$mode}:entryType:{$type->getContext()->id}")) {
                    $return = true;
                }
            }
        }

//        xdebug_stop_trace();
//        \Yii::endProfile('sectionCan::'.$id, 'sectionCan::'.$id);
        return $return;
    }

}