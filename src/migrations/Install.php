<?php

namespace markhuot\CraftQL\migrations;

use craft\db\Migration;

class Install extends Migration
{
    public function safeUp()
    {
        if (!$this->db->tableExists('{{%craftql_tokens}}')) {
            // create the craftql_tokens table
            $this->createTable('{{%craftql_tokens}}', [
                'id' => $this->primaryKey(),
                'userId' => $this->integer()->notNull(),
                'name' => $this->char(128),
                'token' => $this->char(64)->notNull(),
                'scopes' => $this->text(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
            ]);
        }
    }

    public function safeDown()
    {
        if ($this->db->tableExists('{{%craftql_tokens}}')) {
            $this->dropTable('{{%craftql_tokens}}');
        }
    }
}
