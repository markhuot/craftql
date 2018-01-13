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
        $this->addIntField('id')->nonNull();
        $this->addStringField('name')->nonNull();
        $this->addStringField('fullName');
        $this->addStringField('friendlyName')->nonNull();
        $this->addStringField('firstName');
        $this->addStringField('lastName');
        $this->addStringField('username')->nonNull();
        $this->addStringField('email')->nonNull();
        $this->addBooleanField('admin')->nonNull();
        $this->addBooleanField('isCurrent')->nonNull();
        $this->addStringField('preferredLocale');
        // $this->addField('status')->type(UsersField::statusEnum())->nonNull();
        $this->addDateField('dateCreated')->nonNull();
        $this->addDateField('dateUpdated')->nonNull();
        $this->addDateField('lastLoginDate')->nonNull();
    }

}