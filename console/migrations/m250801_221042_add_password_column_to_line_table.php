<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%line}}`.
 */
class m250801_221042_add_password_column_to_line_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%line}}', 'password', $this->string()->after('sip_num'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%line}}', 'password');
    }
}
