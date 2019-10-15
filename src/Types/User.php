<?php

namespace markhuot\CraftQL\Types;

use Craft;
use markhuot\CraftQL\Builders\Schema;
use craft\elements\User as CraftUserElement;

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

        $volumeId = Craft::$app->getProjectConfig()->get('users.photoVolumeId');
        if ($volumeId) {
            $this->addField('photo')
                ->type($this->request->volumes()->get($volumeId));
        }

        $this->addDateField('dateCreated')->nonNull();
        $this->addDateField('dateUpdated')->nonNull();
        $this->addDateField('lastLoginDate')->nonNull();

        $fieldLayoutId = Craft::$app->getFields()->getLayoutByType(CraftUserElement::class)->id;
        $this->addFieldsByLayoutId($fieldLayoutId);

        if ($this->request->token()->can('query:userPermissions')) {
            $this->addStringField('permissions')->lists()->resolve(function ($root, $args, $context, $info) {
                /** @var \craft\elements\User $root */
                return Craft::$app->getUserPermissions()->getPermissionsByUserId($root->id);
            });
        }
    }

}
