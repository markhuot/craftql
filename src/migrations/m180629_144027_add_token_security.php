<?php

namespace markhuot\CraftQL\migrations;

use Craft;
use craft\db\Migration;

/**
 * m180629_144027_add_token_security migration.
 */
class m180629_144027_add_token_security extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('craftql_tokens', 'security', $this->string(2048, '{}'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180629_144027_add_token_security cannot be reverted.\n";
        return false;
    }
}
