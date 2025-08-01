<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "line_tariff".
 *
 * @property int $id
 * @property string $name
 * @property int|null $type
 * @property int|null $default_call_tariff_id
 * @property int|null $price
 * @property int $created_at
 * @property int $updated_at
 *
 * @property CallTariff $defaultCallTariff
 * @property Line[] $lines
 */
class LineTariff extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'line_tariff';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'default_call_tariff_id', 'price'], 'default', 'value' => null],
            [['name'], 'required'],
            [['type', 'default_call_tariff_id', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['default_call_tariff_id'], 'exist', 'skipOnError' => true, 'targetClass' => CallTariff::class, 'targetAttribute' => ['default_call_tariff_id' => 'id']],
            [["price"], "number"]
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'type' => 'Type',
            'default_call_tariff_id' => 'Default Call Tariff',
            'price' => 'Price',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[DefaultCallTariff]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDefaultCallTariff()
    {
        return $this->hasOne(CallTariff::class, ['id' => 'default_call_tariff_id']);
    }

    /**
     * Gets query for [[Lines]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLines()
    {
        return $this->hasMany(Line::class, ['tariff_id' => 'id']);
    }

}
