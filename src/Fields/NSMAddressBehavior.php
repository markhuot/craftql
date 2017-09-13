<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class NSMAddressBehavior extends DefaultBehavior
{
    static $addressObject;

    private static function object() {
        if (!empty(static::$addressObject)) {
            return static::$addressObject;
        }

        return static::$addressObject = new ObjectType([
            'name' => 'NSMAddressModel',
            'fields' => [
                'countryCode' => Type::string(),
                'administrativeArea' => Type::string(),
                'locality' => Type::string(),
                'dependentLocality' => Type::string(),
                'postalCode' => Type::string(),
                'sortingCode' => Type::string(),
                'addressLine1' => Type::string(),
                'addressLine2' => Type::string(),
                'organization' => Type::string(),
                'recipient' => Type::string(),
                'locale' => Type::string(),
                'placeData' => Type::string(),
                'latitude' => Type::string(),
                'longitude' => Type::string(),
                'mapUrl' => Type::string(),
            ]
        ]);
    }

    public function getGraphQLDefaultFieldType($token, $field) {
        return static::object();
    }

    public function getGraphQLDefaultFieldResolver($token, $field, $root, $args) {
        return $root->{$field->handle};
    }

}