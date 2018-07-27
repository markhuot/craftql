<?php

namespace markhuot\CraftQL\Types;

use Firebase\JWT\JWT;
use markhuot\CraftQL\Builders\Schema;
use markhuot\CraftQL\CraftQL;

class Authorize extends Schema {

    function boot() {
        $this->addField('user')->type(User::class);
        $this->addStringField('token');
    }

}