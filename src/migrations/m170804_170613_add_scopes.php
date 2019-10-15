<?php

namespace markhuot\CraftQL\migrations;

use craft\db\Migration;

/**
 * m170804_170613_add_scopes migration.
 */
class m170804_170613_add_scopes extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('craftql_tokens', 'scopes', $this->string(2048, '[]'));
        $this->dropColumn('craftql_tokens', 'isWritable');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m170804_170613_add_scopes cannot be reverted.\n";
        return false;
    }
}
