<?php

namespace markhuot\CraftQL\Models;

use craft\db\ActiveRecord;

class Token extends ActiveRecord
{
    /**
     * @return string The associated database table name
     */
    public static function tableName(): string
    {
        return '{{%craftql_tokens}}';
    }
}