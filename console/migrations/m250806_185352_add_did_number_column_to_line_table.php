<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%line}}`.
 */
class m250806_185352_add_did_number_column_to_line_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%line}}', 'did_number', $this->string()->after('password'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%line}}', 'did_number');
    }
}
