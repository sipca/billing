<?php

namespace common\models;

/**
 * This is the model class for table "number_prefix".
 *
 * @property int $id
 * @property int|null $call_tariff_id
 * @property string|null $prefix
 *
 * @property CallTariff $callTariff
 */
class NumberPrefix extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'number_prefix';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['call_tariff_id', 'prefix'], 'default', 'value' => null],
            [['call_tariff_id'], 'integer'],
            [['prefix'], 'string', 'max' => 255],
            [['call_tariff_id'], 'exist', 'skipOnError' => true, 'targetClass' => CallTariff::class, 'targetAttribute' => ['call_tariff_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'call_tariff_id' => 'Call Tariff ID',
            'prefix' => 'Prefix',
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

}
