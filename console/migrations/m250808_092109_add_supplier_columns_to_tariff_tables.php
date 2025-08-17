<?php

use yii\db\Migration;

class m250808_092109_add_supplier_columns_to_tariff_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("call_tariff", "supplier_price_in", $this->integer()->after('price_in'));
        $this->addColumn("call_tariff", "supplier_price_out", $this->integer()->after('price_out'));
        $this->addColumn("call_tariff", "supplier_connection_price_in", $this->integer()->after('price_out'));
        $this->addColumn("call_tariff", "supplier_connection_price_out", $this->integer()->after('supplier_connection_price_in'));
        $this->addColumn("call_tariff", "supplier_type", $this->integer()->after('type'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250808_092109_add_supplier_columns_to_tariff_tables cannot be reverted.\n";

        return false;
    }
}
