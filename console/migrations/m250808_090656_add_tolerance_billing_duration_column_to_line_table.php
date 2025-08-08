<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%line}}`.
 */
class m250808_090656_add_tolerance_billing_duration_column_to_line_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%line}}', 'tolerance_billing_duration', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%line}}', 'tolerance_billing_duration');
    }
}
