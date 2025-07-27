<?php

use yii\db\Migration;

class m250707_175456_alter_price_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("call_tariff", "price_in", $this->integer()->after("price"));
        $this->renameColumn("call_tariff", "price", "price_out");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250707_175456_alter_price_column cannot be reverted.\n";

        return false;
    }
}
