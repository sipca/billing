<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "line_to_user".
 *
 * @property int $user_id
 * @property int $line_id
 *
 * @property Line $line
 * @property User $user
 */
class LineToUser extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'line_to_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'line_id'], 'required'],
            [['user_id', 'line_id'], 'integer'],
            [['user_id', 'line_id'], 'unique', 'targetAttribute' => ['user_id', 'line_id']],
            [['line_id'], 'exist', 'skipOnError' => true, 'targetClass' => Line::class, 'targetAttribute' => ['line_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'line_id' => 'Line ID',
        ];
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

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

}
