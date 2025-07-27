<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%call_tariff_to_line}}`.
 */
class m250528_114920_create_call_tariff_to_line_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%call_tariff_to_line}}', [
            'call_tariff_id' => $this->integer(),
            "line_id" => $this->integer(),
            "PRIMARY KEY(call_tariff_id, line_id)",
        ]);

        $this->createIndex("idx-call_tariff_to_line-call_tariff_id", "call_tariff_to_line", "call_tariff_id");
        $this->createIndex("idx-call_tariff_to_line-line_id", "call_tariff_to_line", "line_id");

        $this->addForeignKey("fk-call_tariff_to_line-call_tariff_id", "call_tariff_to_line", "call_tariff_id", "call_tariff", "id", "CASCADE");
        $this->addForeignKey("fk-call_tariff_to_line-line_id", "call_tariff_to_line", "line_id", "line", "id", "CASCADE");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%call_tariff_to_line}}');
    }
}
