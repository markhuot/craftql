<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType as GraphQLObjectType;
use GraphQL\Type\Definition\InterfaceType as GraphQLInterfaceType;
use markhuot\CraftQL\Request;

class ObjectType extends GraphQLObjectType {

    static $singletons = [];
    static $builtInterfaces = [];

    public $name = null;
    protected $fields = [];
    protected $interfaces = [];
    protected $craftType;

    function __construct(Request $request, $craftType=null) {
        $this->craftType = $craftType;

        $config = [
            'name' => $this->name($request),
            'fields' => $this->fields($request),
            'interfaces' => $this->interfaces($request),
        ];

        if ($craftType) {
            $config['craftType'] = $craftType;
        }

        parent::__construct($config);
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

    protected function interfaces(Request $request) {
        return $this->interfaces;
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

    static function baseFields(Request $request) {
        return [];
    }

    static function interface(Request $request) {
        $reflect = new \ReflectionClass(static::class);
        $shortName = $reflect->getShortName();

        if (!empty(static::$builtInterfaces[$shortName])) {
            return static::$builtInterfaces[$shortName];
        }

        return static::$builtInterfaces[$shortName] = new GraphQLInterfaceType([
            'name' => $shortName.'Interface',
            'description' => 'An entry in Craft',

            // this has to be a callback because the `user` field references a User type
            // that could have an Entries custom field. This is a problem because we have
            // a circullar reference. Our EntryInterface defines a User which defines an
            // Entries field which relies on the EntryInterface. The callback here ensures
            // that the nested Entries field gets a resolved interface.
            'fields' => function () use ($request) {
                return static::baseFields($request);
            },

            'resolveType' => function ($entry) {
                return static::resolveType($entry);
            }
        ]);
    }

}