<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Request;

class CategoryGroup extends ObjectType {

    protected function fields(Request $request) {
        return [
            'id' => ['type' => Type::int()],
            'name' => ['type' => Type::string()],
            'handle' => ['type' => Type::string()],
        ];
    }

}