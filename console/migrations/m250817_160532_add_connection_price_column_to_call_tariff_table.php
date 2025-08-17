<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%call_tariff}}`.
 */
class m250817_160532_add_connection_price_column_to_call_tariff_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%call_tariff}}', 'price_connection_in', $this->integer()->after('type'));
        $this->addColumn('{{%call_tariff}}', 'price_connection_out', $this->integer()->after('price_connection_in'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%call_tariff}}', 'price_connection_in');
        $this->dropColumn('{{%call_tariff}}', 'price_connection_out');
    }
}
