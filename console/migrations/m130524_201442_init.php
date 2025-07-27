<?php

use yii\db\Migration;

class m130524_201442_init extends Migration
{
    public function up()
    {
        // Таблица user
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'access_token' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'email' => $this->string()->notNull()->unique(),
            'balance' => $this->integer(),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'role' => $this->smallInteger(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-user-status', '{{%user}}', 'status');


// Таблица line_tariff
        $this->createTable('line_tariff', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'type' => $this->smallInteger(),
            'default_call_tariff_id' => $this->integer(),
            'price' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-line_tariff-type', 'line_tariff', 'type');

// FK: default_call_tariff_id → call_tariff(id)
// Таблица call_tariff
        $this->createTable('call_tariff', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'type' => $this->smallInteger(),
            'price' => $this->integer(),
            'number_start_with' => $this->string(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-call_tariff-type', 'call_tariff', 'type');
        $this->createIndex('idx-call_tariff-name', 'call_tariff', 'name');

        $this->addForeignKey(
            'fk-line_tariff-default_call_tariff_id',
            'line_tariff',
            'default_call_tariff_id',
            'call_tariff',
            'id',
            'SET NULL',
            'CASCADE'
        );

// Таблица line
        $this->createTable('line', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'sip_num' => $this->integer(),
            'tariff_id' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-line-name', 'line', 'name', true);
        $this->createIndex('idx-line-tariff_id', 'line', 'tariff_id');

// FK: tariff_id → line_tariff(id)
        $this->addForeignKey(
            'fk-line-tariff_id',
            'line',
            'tariff_id',
            'line_tariff',
            'id',
            'SET NULL',
            'CASCADE'
        );

// Таблица call
        $this->createTable('call', [
            'id' => $this->primaryKey(),
            'call_id' => $this->string(),
            'line_id' => $this->integer(),
            'tariff_id' => $this->integer(),
            'source' => $this->string(),
            'destination' => $this->string(),
            'direction' => $this->string(),
            'record_link' => $this->string(),
            'duration' => $this->integer(),
            'billing_duration' => $this->integer(),
            'status' => $this->smallInteger(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-call-call_id', 'call', 'call_id', true);
        $this->createIndex('idx-call-line_id', 'call', 'line_id');
        $this->createIndex('idx-call-tariff_id', 'call', 'tariff_id');

// FK: line_id → line(id)
        $this->addForeignKey(
            'fk-call-line_id',
            'call',
            'line_id',
            'line',
            'id',
            'CASCADE',
            'CASCADE'
        );

// FK: tariff_id → call_tariff(id)
        $this->addForeignKey(
            'fk-call-tariff_id',
            'call',
            'tariff_id',
            'call_tariff',
            'id',
            'CASCADE',
            'CASCADE'
        );

// Таблица line_to_user
        $this->createTable('line_to_user', [
            'user_id' => $this->integer()->notNull(),
            'line_id' => $this->integer()->notNull(),
            'PRIMARY KEY(user_id, line_id)',
        ]);

        $this->createIndex('idx-line_to_user-user_id', 'line_to_user', 'user_id');
        $this->createIndex('idx-line_to_user-line_id', 'line_to_user', 'line_id');

// FK: user_id → user(id)
        $this->addForeignKey(
            'fk-line_to_user-user_id',
            'line_to_user',
            'user_id',
            'user',
            'id',
            'CASCADE',
            'CASCADE'
        );

// FK: line_id → line(id)
        $this->addForeignKey(
            'fk-line_to_user-line_id',
            'line_to_user',
            'line_id',
            'line',
            'id',
            'CASCADE',
            'CASCADE'
        );

// Таблица transaction
        $this->createTable('transaction', [
            'id' => $this->primaryKey(),
            'uuid' => $this->string(),
            'type' => $this->smallInteger(),
            'sum' => $this->integer(),
            'description' => $this->string(),
            'status' => $this->smallInteger(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-transaction-uuid', 'transaction', 'uuid', true);
        $this->createIndex('idx-transaction-type', 'transaction', 'type');
        $this->createIndex('idx-transaction-status', 'transaction', 'status');

    }

    public function down()
    {
        $this->dropTable('{{%user}}');
    }
}
