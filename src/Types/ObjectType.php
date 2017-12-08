<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType as GraphQLObjectType;
use markhuot\CraftQL\Request;

class ObjectType extends GraphQLObjectType {

    static $singletons = [];

    public $name = null;
    protected $fields = [];

    function __construct(Request $request) {
        parent::__construct([
            'name' => $this->name($request),
            'fields' => $this->fields($request),
        ]);
    }

    protected function name(Request $request):string {
        if ($this->name == null) {
            $reflect = new \ReflectionClass(static::class);
            return $this->name = $reflect->getShortName();
        }

        return $this->name;
    }

    protected function fields(Request $request) {
        return $this->fields;
    }

    static function singleton(Request $request, $key=null) {
        if ($key === null) {
            $key = static::class;
        }

        if (!empty(self::$singletons[$key])) {
            return self::$singletons[$key];
        }

        return self::$singletons[$key] = new static($request);
    }

}