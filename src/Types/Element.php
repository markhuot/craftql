<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Builders\Schema;
use markhuot\CraftQL\Request;
use markhuot\CraftQL\Services\FieldService;

class Element extends Schema {

    function boot() {
        /** @var FieldService $fieldService */
        $fieldService = \Yii::$container->get('craftQLFieldService');
        $this->fields = array_merge($this->fields, $fieldService->getAllFields($this->request));
    }

}