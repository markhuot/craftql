<?php

namespace markhuot\CraftQL\GraphQLFields\Query;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\EnumType;
use markhuot\CraftQL\GraphQLFields\Base as BaseField;
use markhuot\CraftQL\Types\Section;

class Sections extends BaseField {

    protected $description = 'Sections defined in Craft';

    function getType() {
        return Type::listOf(Section::type());
    }

    function getResolve($root, $args, $context, $info) {
        return \Craft::$app->sections->getAllSections();
    }

}