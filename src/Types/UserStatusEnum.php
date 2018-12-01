<?php

namespace markhuot\CraftQL\Types;

/**
 * Class UserStatusEnum
 * @package markhuot\CraftQL\Types
 * @craftql-type enum
 */
class UserStatusEnum {

    const ACTIVE = 'active';
    const LOCKED = 'locked';
    const SUSPENDED = 'suspended';
    const PENDING = 'pending';

}