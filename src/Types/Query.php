<?php

namespace markhuot\CraftQL\Types;

use craft\fields\data\ColorData;
use markhuot\CraftQL\Builder2\GraphQlObject;
use markhuot\CraftQL\Annotations\CraftQL;

class Query {

    /**
     * A simple way to determine if your GraphQL instance is working
     *
     * @CraftQL
     * @var string
     */
    public $helloWorld = 'static return?';

    /**
     * Test of the ColorData type
     *
     * @CraftQL
     * @return ColorData
     */
    function getColor() {
        return new ColorData('#ff0000');
    }

    /**
     * Pull entries out of Craft just like `craft.entries`
     *
     * @param $root
     * @param $args
     * @param $context
     * @param $info
     * @return EntryInterface[]
     */
    public function resolveEntriesField($root, $args, $context, $info) {
        var_dump('here i am!');
        die;
    }

}