<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $auth_key
 * @property string $access_token
 * @property string $password_hash
 * @property string $email
 * @property int|null $balance
 * @property int $status
 * @property int $role
 * @property int $created_at
 * @property int $updated_at
 *
 * @property LineToUser[] $lineToUsers
 * @property Line[] $lines
 * @property Transaction[] $transactions
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    const ROLE_ADMIN = 10;
    const ROLE_USER = 1;

    const STATUS_ON = 10;
    const STATUS_OFF = 0;

    const ROLES = [
        self::ROLE_USER => "User",
        self::ROLE_ADMIN => "Admin",
    ];

    const STATUSES = [
        self::STATUS_ON => "On",
        self::STATUS_OFF => "Off",
    ];

    public $password;
    public $_lines;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['balance'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 10],
            [['username', 'auth_key', 'password_hash', 'email'], 'required'],
            [['balance', 'status', 'created_at', 'updated_at', 'role'], 'integer'],
            [['username', 'password_hash', 'email', 'password'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [["_lines"], "safe"]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'email' => 'Email',
            'balance' => 'Balance',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[LineToUsers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLineToUsers()
    {
        return $this->hasMany(LineToUser::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Lines]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLines()
    {
        return $this->hasMany(Line::class, ['id' => 'line_id'])->viaTable('line_to_user', ['user_id' => 'id']);
    }

    public function getTransactions()
    {
        return $this->hasMany(Transaction::class, ['user_id' => 'id']);
    }

    public function afterFind()
    {
        parent::afterFind();
        foreach ($this->lines as $line) {
            $this->_lines[$line->id] = true;
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        LineToUser::deleteAll(["user_id" => $this->id]);

        if($this->_lines) {
            foreach ($this->_lines as $line_id => $value) {
                if($value) {
                    $lineToUser = new LineToUser(["user_id" => $this->id, "line_id" => $line_id]);
                    $lineToUser->save();
                }
            }
        }
    }

    public static function findByUsername($username)
    {
        return self::findOne(['username' => $username]);
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return self::findOne(['access_token' => $token]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }
}
