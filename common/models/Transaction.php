<?php

namespace common\models;

use common\enums\TransactionStatusEnum;
use common\enums\TransactionTypeEnum;
use jp3cki\uuid\Uuid;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "transaction".
 *
 * @property int $id
 * @property string|null $uuid
 * @property int|null $type
 * @property int|null $user_id
 * @property int|null $sum
 * @property string|null $description
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $user
 */
class Transaction extends \yii\db\ActiveRecord
{

    public $minus;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transaction';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uuid', 'type', 'sum', 'description', 'status'], 'default', 'value' => null],
            [['type', 'sum', 'status', 'created_at', 'updated_at', 'user_id', 'minus'], 'integer'],
            [['uuid', 'description'], 'string', 'max' => 255],
            [['uuid'], 'unique'],
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
            'uuid' => 'Uuid',
            'type' => 'Type',
            'sum' => 'Sum',
            'description' => 'Description',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public static function create(int $user_id, TransactionTypeEnum $type, float $sum, string $description, TransactionStatusEnum $status): Transaction
    {
        $model = new self([
            'user_id' => $user_id,
            'type' => $type->value,
            'sum' => $sum,
            'description' => $description,
            'status' => $status->value,
            "uuid" => Uuid::v4()->formatAsString()
        ]);
        if(!$model->save()) {
            Yii::error($model->errors, "import-calls");
        } else {
            $model->user->balance += $sum;
            $model->user->update();
        }

        return $model;
    }

    public function afterDelete()
    {
        $this->user->balance -= $this->sum;
        $this->user->update();
    }

}
