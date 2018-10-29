<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\Builder2\GraphQlObject;
use markhuot\CraftQL\Annotations\CraftQL;

class Query {

    /**
     * A simple way to determine if your GraphQL instance is working
     *
     * @var string
     */
    public $helloWorld = 'static return?';

    /**
     * Test of the ColorData type
     *
     * @return \craft\fields\data\ColorData
     */
    function getColor() {
        return new ColorData('#ff0000');
    }

    /**
     * Returns the entries
     *
     * @CraftQL()
     * @return \craft\elements\Entry[]
     */
    function getEntries() {
        return \craft\elements\Entry::find()->all();
    }

}