<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%number_prefix}}`.
 */
class m250817_165225_create_number_prefix_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%number_prefix}}', [
            'id' => $this->primaryKey(),
            'call_tariff_id' => $this->integer(),
            'prefix' => $this->string(),
        ]);

        $this->createIndex("idx-number_prefix-call_tariff_id", "{{%number_prefix}}", "call_tariff_id");
        $this->addForeignKey("fk-number_prefix-call_tariff_id", "{{%number_prefix}}", "call_tariff_id",'call_tariff', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%number_prefix}}');
    }
}
