<?php

namespace markhuot\CraftQL\GraphQLFields\Query;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Types\TagConnection;
use markhuot\CraftQL\Types\Entry;

class TagsConnection extends Tags {

    use ConnectionResolverTrait;

    protected $description = 'Tags through a Connection in Craft';

    function getType() {
        return TagConnection::make($this->request);
    }

}