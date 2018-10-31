<?php

namespace markhuot\CraftQL\Behaviors;

use yii\base\Behavior;

class Category extends Behavior {

    /**
     * @var \craft\elements\Category
     */
    public $owner;

    protected function getCriteria($root, $args, $context, $info, $criteria=null) {
        $criteria = $criteria ?: \craft\elements\Category::find();

        if (isset($args['group'])) {
            $args['groupId'] = $args['group'];
            unset($args['group']);
        }

        foreach ($args as $key => $value) {
            $criteria = $criteria->{$key}($value);
        }

        return $criteria;
    }

    // function getCraftQLChildren($request, $root, $args, $context, $info) {
    //     $this->getCriteria($root, $args, $context, $info, $this->owner->getChildren());
    // }

    // function getCraftQLChildrenConnection($request, $root, $args, $context, $info) {
    //     list($pageInfo, $categories) = \craft\helpers\Template::paginateCriteria($this->getCriteria($root, $args, $context, $info, $this->owner->getChildren()));
    //     $pageInfo->limit = @$args['limit'] ?: 100;
    //
    //     return [
    //         'totalCount' => $pageInfo->total,
    //         'pageInfo' => $pageInfo,
    //         'edges' => $categories,
    //     ];
    // }

}