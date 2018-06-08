<?php

namespace markhuot\CraftQL\Types;

use Firebase\JWT\JWT;
use markhuot\CraftQL\Builders\Schema;
use markhuot\CraftQL\CraftQL;

class Authorize extends Schema {

    function boot() {

        $this->addField('user')
            ->type(User::class)
            ->resolve(function ($root, $args) {
                return $root['user'];
            });

        $this->addStringField('token')
            ->resolve(function ($root, $args) {
                /** @var \craft\elements\User $user */
                $user = $root['user'];

                $userRow = (new \craft\db\Query())
                    ->from('users')
                    ->where(['id' => $user->id])
                    ->limit(1)
                    ->one();

                return CraftQL::getInstance()->jwt->encode([
                    'uid' => $userRow['uid'],
                    // @TODO add expiration in to this
                ]);
            });

    }

}