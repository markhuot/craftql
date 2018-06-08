<?php

namespace markhuot\CraftQL\Types;

use Firebase\JWT\JWT;
use markhuot\CraftQL\Builders\Schema;

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

                $key = \Craft::$app->config->general->securityKey;
                if (!empty(\Craft::$app->config->craftql->securityKey)) {
                    $key = \Craft::$app->config->craftql->securityKey;
                }

                $userRow = (new \craft\db\Query())
                    ->from('users')
                    ->where(['id' => $user->id])
                    ->limit(1)
                    ->one();

                $token = [
                    'uid' => $userRow['uid'],
                    // @TODO add expiration in to this
                ];

                return JWT::encode($token, $key);
            });

    }

}