<?php

namespace markhuot\CraftQL\Types;

use Craft;
use craft\elements\User as CraftUserElement;

class User extends ProxyObject {

    /**
     * @var int
     * @craftql-nonNull
     */
    public $id;

    /**
     * @var string
     * @craftql-nonNull
     */
    public $name;

    /**
     * @var string
     */
    public $fullName;

    /**
     * @var string
     * @craftql-nonNull
     */
    public $friendlyName;

    /**
     * @var string
     */
    public $firstName;

    /**
     * @var string
     */
    public $lastName;

    /**
     * @var string
     * @craftql-nonNull
     */
    public $username;

    /**
     * @var string
     * @craftql-nonNull
     */
    public $email;

    /**
     * @var boolean
     * @craftql-nonNull
     */
    public $admin;

    /**
     * @var boolean
     * @craftql-nonNull
     */
    public $isCurrent;

    /**
     * @var string
     */
    public $preferredLocale;

    /**
     * @var Timestamp
     */
    public $dateCreated;

    /**
     * @var Timestamp
     */
    public $dateUpdated;

    /**
     * @var Timestamp
     */
    public $lastLoginDate;

    /**
     * @craftql-nonNull
     * @var UserStatusEnum
     */
    public $status;

    // $volumeId = Craft::$app->getSystemSettings()->getSetting('users', 'photoVolumeId');
    // if ($volumeId) {
    //     $this->addField('photo')
    //         ->type($this->request->volumes()->get($volumeId));
    // }

    // $fieldLayoutId = Craft::$app->getFields()->getLayoutByType(CraftUserElement::class)->id;
    // $this->addFieldsByLayoutId($fieldLayoutId);

    /**
     * @return string[]
     * @todo make conditional based on token scopes
     */
    function getPermissions() {
        return \Craft::$app->getUserPermissions()->getPermissionsByUserId($this->source->id);
    }

}