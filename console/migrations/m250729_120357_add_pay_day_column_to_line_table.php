<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%line}}`.
 */
class m250729_120357_add_pay_day_column_to_line_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('line', 'pay_billing_day', $this->integer()->after('sip_num'));
        $this->addColumn('line', 'pay_date', $this->integer()->after('pay_billing_day'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('line', 'pay_billing_day');
        $this->dropColumn('line', 'pay_date');
    }
}
