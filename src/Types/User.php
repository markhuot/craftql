<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Builders\Schema;
use markhuot\CraftQL\GraphQLFields\Query\Users as UsersField;
use markhuot\CraftQL\GraphQLFields\General\Date as DateField;

class User extends Schema {

    function boot() {
        $this->addRawIntField('id')->nonNull();
        $this->addRawStringField('name')->nonNull();
        $this->addRawStringField('fullName');
        $this->addRawStringField('friendlyName')->nonNull();
        $this->addRawStringField('firstName');
        $this->addRawStringField('lastName');
        $this->addRawStringField('username')->nonNull();
        $this->addRawStringField('email')->nonNull();
        $this->addRawBooleanField('admin')->nonNull();
        $this->addRawBooleanField('isCurrent')->nonNull();
        $this->addRawStringField('preferredLocale');
        // $this->addRawField('status')->type(UsersField::statusEnum())->nonNull();
        $this->addRawDateField('dateCreated')->nonNull();
        $this->addRawDateField('dateUpdated')->nonNull();
        $this->addRawDateField('lastLoginDate')->nonNull();
    }

}