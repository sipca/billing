<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%transaction}}`.
 */
class m250709_143707_add_user_id_column_to_transaction_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%transaction}}', 'user_id', $this->integer()->after('uuid'));
        $this->createIndex("{{%idx-transaction-user_id}}", "{{%transaction}}", "user_id");
        $this->addForeignKey("fk-transaction-user_id", "{{%transaction}}", "user_id","{{%user}}","id","CASCADE","CASCADE");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
