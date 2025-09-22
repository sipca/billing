<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%line}}`.
 */
class m250922_100433_add_delay_sec_column_to_line_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%line}}', 'delay_sec', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%line}}', 'delay_sec');
    }
}
