<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "line".
 *
 * @property int $id
 * @property string $name
 * @property int|null $tariff_id
 * @property int|null $sip_num
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Call[] $calls
 * @property LineToUser[] $lineToUsers
 * @property LineTariff $tariff
 * @property User[] $users
 */
class Line extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'line';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tariff_id'], 'default', 'value' => null],
            [['name'], 'required'],
            [['tariff_id', 'created_at', 'updated_at', "sip_num"], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique'],
            [['tariff_id'], 'exist', 'skipOnError' => true, 'targetClass' => LineTariff::class, 'targetAttribute' => ['tariff_id' => 'id']],
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
            'tariff_id' => 'Tariff',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Calls]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCalls()
    {
        return $this->hasMany(Call::class, ['line_id' => 'id']);
    }

    /**
     * Gets query for [[LineToUsers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLineToUsers()
    {
        return $this->hasMany(LineToUser::class, ['line_id' => 'id']);
    }

    /**
     * Gets query for [[Tariff]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTariff()
    {
        return $this->hasOne(LineTariff::class, ['id' => 'tariff_id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->viaTable('line_to_user', ['line_id' => 'id']);
    }

}
