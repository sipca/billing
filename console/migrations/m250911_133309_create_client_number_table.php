<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%client_number}}`.
 */
class m250911_133309_create_client_number_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%client_number}}', [
            'id' => $this->primaryKey(),
            'number' => $this->string(),
            'name' => $this->string(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);

        $this->createIndex('idx-client_number-number', '{{%client_number}}', 'number');
        $this->createIndex('idx-client_number-created_by', '{{%client_number}}', 'created_by');
        $this->createIndex('idx-client_number-updated_by', '{{%client_number}}', 'updated_by');
        $this->addForeignKey('fk-client_number-created_by', '{{%client_number}}', 'created_by', '{{%user}}', 'id', 'SET NULL');
        $this->addForeignKey('fk-client_number-updated_by', '{{%client_number}}', 'updated_by', '{{%user}}', 'id', 'SET NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%client_number}}');
    }
}
