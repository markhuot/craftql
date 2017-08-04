<?php

namespace markhuot\CraftQL\Models;

use craft\db\ActiveRecord;

class Token extends ActiveRecord
{
    public static function forUser(): Token
    {
        $token = new static;
        $token->scopes = json_encode([
            'query:entries' => 1,
            'query:users' => 1,
        ]);
        return $token;
    }

    /**
     * @return string The associated database table name
     */
    public static function tableName(): string
    {
        return '{{%craftql_tokens}}';
    }

    function getScopeArray(): array
    {
        return json_decode($this->scopes, true);
    } 

    function can($do): bool {
        return @$this->scopeArray[$do] ?: false;
    }
}