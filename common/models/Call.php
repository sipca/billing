<?php

namespace common\models;

use common\enums\CallStatusEnum;
use common\enums\CallTariffTypeEnum;
use common\enums\TransactionStatusEnum;
use common\enums\TransactionTypeEnum;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "call".
 *
 * @property int $id
 * @property string|null $call_id
 * @property int $line_id
 * @property int $tariff_id
 * @property string|null $source
 * @property string|null $destination
 * @property string|null $record_link
 * @property int|null $duration
 * @property int|null $billing_duration
 * @property int|null $status
 * @property string $direction
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Line $line
 * @property CallTariff $tariff
 */
class Call extends \yii\db\ActiveRecord
{
    const string DIRECTION_IN = 'in';
    const string DIRECTION_OUT = 'out';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'call';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['call_id', 'source', 'destination', 'record_link', 'duration', 'billing_duration', 'status'], 'default', 'value' => null],
            [['line_id', 'tariff_id', 'duration', 'billing_duration', 'status', 'created_at', 'updated_at'], 'integer'],
            [['call_id', 'source', 'destination', 'record_link', 'direction'], 'string', 'max' => 255],
            [['call_id'], 'unique'],
            [['line_id'], 'exist', 'skipOnError' => true, 'targetClass' => Line::class, 'targetAttribute' => ['line_id' => 'id']],
            [['tariff_id'], 'exist', 'skipOnError' => true, 'targetClass' => CallTariff::class, 'targetAttribute' => ['tariff_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'call_id' => 'ID',
            'line_id' => 'Line',
            'tariff_id' => 'Tariff',
            'source' => 'Source',
            'destination' => 'Destination',
            'record_link' => 'Record',
            'duration' => 'Duration',
            'billing_duration' => 'Billing Duration',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
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
     * Gets query for [[Tariff]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTariff()
    {
        return $this->hasOne(CallTariff::class, ['id' => 'tariff_id']);
    }

    public function beforeSave($insert)
    {
        if($this->isNewRecord && $this->tariff_id === null) {
            if($this?->line?->tariff?->default_call_tariff_id) {
                $this->tariff_id = $this?->line?->tariff?->default_call_tariff_id;
            } else if(env('DEFAULT_CALL_TARIFF_ID')) {
                $this->tariff_id = env('DEFAULT_CALL_TARIFF_ID');
            }
        }

        if($this->status === CallStatusEnum::ANSWERED->value && $this?->line?->tolerance_billing_duration) {
            $this->billing_duration += $this?->line?->tolerance_billing_duration;
        }
        return parent::beforeSave($insert);
    }

    public function getSum() : float
    {
        if($this->tariff) {
            $conn_price = $this->status === CallStatusEnum::ANSWERED->value ? $this->tariff->price_connection_in : 0;
            if($this->direction === self::DIRECTION_OUT) {
                $conn_price = $this->status === CallStatusEnum::ANSWERED->value ? $this->tariff->price_connection_out : 0;
                return match ($this->tariff->type) {
                    CallTariffTypeEnum::MIN_MIN->value => round($conn_price + $this->tariff->price_out * (ceil($this->billing_duration / 60)), 2),
                    default => round($conn_price + $this->tariff->price_out * ($this->billing_duration / 60), 2),
                };
            }
            return match ($this->tariff->type) {
                CallTariffTypeEnum::MIN_MIN->value => round($conn_price + $this->tariff->price_in * (ceil($this->billing_duration / 60)), 2),
                default => round($conn_price + $this->tariff->price_in * ($this->billing_duration / 60), 2),
            };
        }
        return 0;
    }

    public function getSumSupplier() : float
    {
        if($this->tariff) {
            $conn_price = $this->status === CallStatusEnum::ANSWERED->value ? $this->tariff->supplier_connection_price_in : 0;
            if($this->direction === self::DIRECTION_OUT) {
                $conn_price = $this->status === CallStatusEnum::ANSWERED->value ? $this->tariff->supplier_connection_price_out : 0;

                return match ($this->tariff->supplier_type) {
                    CallTariffTypeEnum::MIN_MIN->value => round($conn_price + $this->tariff->supplier_price_out * (ceil($this->billing_duration / 60)), 2),
                    default => round($conn_price + $this->tariff->supplier_price_out * ($this->billing_duration / 60), 2),
                };
            }
            return match ($this->tariff->supplier_type) {
                CallTariffTypeEnum::MIN_MIN->value => round($conn_price + $this->tariff->supplier_price_in * (ceil($this->billing_duration / 60)), 2),
                default => round($conn_price + $this->tariff->supplier_price_in * ($this->billing_duration / 60), 2),
            };
        }
        return 0;
    }

    public function getRecordPath() : string
    {
        $y = Yii::$app->formatter->asDate($this->created_at, 'php:Y');
        $m = Yii::$app->formatter->asDate($this->created_at, 'php:m');
        $d = Yii::$app->formatter->asDate($this->created_at, 'php:d');

        return "/monitor/$y/$m/$d/$this->record_link";
    }

    public function getRecord() : string
    {
        $src = $this->getRecordPath();

        if(is_file($src) && file_exists($src)) {
            $base64 = base64_encode(file_get_contents($src));

            $audio = '
<audio controls>
  <source src="data:audio/wav;base64,'.$base64.'" type="audio/wav">
  Your browser does not support the audio tag.
</audio>
<a href="/call/download-audio?call_id='.$this->call_id.'&name='.$this->call_id.'.wav" target="_blank"><i class="fas fa-download"></i></a>
';
        } else {
            $audio = '';
        }

        return $audio;
    }

    public function charge() : void
    {
        $string = "Automatic charge";

        if(!$this?->line?->users) return;

        foreach ($this->line->users as $user) {
            $user = $user->id;
            break;
        }

        if(isset($user)) {
            $sum = round($this->getSum());
            if($sum > 0) {
                Transaction::create($user, TransactionTypeEnum::AUTOMATIC, -$sum, $string . " " . $this->call_id, TransactionStatusEnum::PAID);
            }
        }
    }
}
