<?php

namespace markhuot\CraftQL\GraphQLFields\Query;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Types\CategoryConnection;
use markhuot\CraftQL\Types\Entry;

class CategoriesConnection extends Categories {

    use ConnectionResolverTrait;

    protected $description = 'Categories through a Connection in Craft';

    function getType() {
        return CategoryConnection::singleton($this->request);
    }

}