<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "call_tariff_to_line".
 *
 * @property int $call_tariff_id
 * @property int $line_id
 *
 * @property CallTariff $callTariff
 * @property Line $line
 */
class CallTariffToLine extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'call_tariff_to_line';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['call_tariff_id', 'line_id'], 'required'],
            [['call_tariff_id', 'line_id'], 'integer'],
            [['call_tariff_id', 'line_id'], 'unique', 'targetAttribute' => ['call_tariff_id', 'line_id']],
            [['call_tariff_id'], 'exist', 'skipOnError' => true, 'targetClass' => CallTariff::class, 'targetAttribute' => ['call_tariff_id' => 'id']],
            [['line_id'], 'exist', 'skipOnError' => true, 'targetClass' => Line::class, 'targetAttribute' => ['line_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'call_tariff_id' => 'Call Tariff',
            'line_id' => 'Line ID',
        ];
    }

    /**
     * Gets query for [[CallTariff]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCallTariff()
    {
        return $this->hasOne(CallTariff::class, ['id' => 'call_tariff_id']);
    }

    /**
     * Gets query for [[Line]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLine()
    {
        return $this->hasOne(Line::class, ['id' => 'line_id']);
    }

}
