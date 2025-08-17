<?php

namespace common\models;

use common\enums\LineTariffEnum;
use common\enums\TransactionStatusEnum;
use common\enums\TransactionTypeEnum;
use DateTime;
use IntlDateFormatter;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "line".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $did_number
 * @property int|null $tariff_id
 * @property int|null $sip_num
 * @property string $password
 * @property int|null $pay_billing_day
 * @property int|null $pay_date
 * @property int|null $tolerance_billing_duration
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
    public $_tariffs;

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
            [['tariff_id', 'created_at', 'updated_at', "sip_num", "pay_billing_day", "pay_date", 'tolerance_billing_duration'], 'integer'],
            [['name', 'password', 'did_number'], 'string', 'max' => 255],
            [['name'], 'unique'],
            [['tariff_id'], 'exist', 'skipOnError' => true, 'targetClass' => LineTariff::class, 'targetAttribute' => ['tariff_id' => 'id']],
            [["tariffs", "description"], "safe"]
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
            'tariff_id' => 'Line Tariff',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'tolerance_billing_duration' => 'Tolerance Billing Duration in seconds',
            "did_number" => 'DID Number',
            'tariffs' => 'Call Tariffs assigned to line',
            'pay_billing_day' => 'Payment billing day',
            'pay_date' => 'Payment date',
            'sip_num' => 'SIP Number',
            'password' => 'SIP Password',
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

    public function addTransactionToUsers() : void
    {
        if(!$this->users || !$this->tariff) return;

        foreach ($this->users as $user) {
            Transaction::create($user->id, TransactionTypeEnum::AUTOMATIC, -$this->tariff->price, $this->name . " " . LineTariffEnum::tryFrom($this->tariff->type)->name, TransactionStatusEnum::PAID);
        }
    }

    public function getPayBillingDayText()
    {
        $formatter = new IntlDateFormatter(
            Yii::$app->formatter->locale,
            IntlDateFormatter::FULL,
            IntlDateFormatter::NONE,
            'UTC',
            IntlDateFormatter::GREGORIAN,
            'EEEE' // Полное название дня недели
        );

        // "Sunday +N days" даёт нужный день, где N = $dayNumber
        $date = new DateTime("Sunday +$this->pay_billing_day days");
        return $formatter->format($date);
    }

    public function getTariffs()
    {
        if(!$this->_tariffs) {
            $this->_tariffs = CallTariffToLine::find()
                ->where(["line_id" => $this->id])
                ->select(["call_tariff_id"])
                ->indexBy("call_tariff_id")
                ->column();
        }
        return $this->_tariffs;
    }

    /**
     * @param mixed $tariffs
     */
    public function setTariffs($tariffs): void
    {
        $this->_tariffs = $tariffs;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        CallTariffToLine::deleteAll(["line_id" => $this->id]);

        if($this->_tariffs) {
            foreach ($this->_tariffs as $tariff) {
                $model = new CallTariffToLine(["line_id" => $this->id, "call_tariff_id" => $tariff]);
                $model->save();
            }
        }
    }

}
