<?php

namespace common\models;

use common\enums\CallTariffTypeEnum;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "call_tariff".
 *
 * @property int $id
 * @property string $name
 * @property int|null $type
 * @property int|null $price_in
 * @property int|null $price_out
 * @property string|null $number_start_with
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Call[] $calls
 * @property LineTariff[] $lineTariffs
 * @property Line[] $lines
 */
class CallTariff extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'call_tariff';
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
            [['type', 'price_in','price_out', 'number_start_with'], 'default', 'value' => null],
            [['name'], 'required'],
            [['type', 'price_in','price_out', 'created_at', 'updated_at'], 'integer'],
            [['name', 'number_start_with'], 'string', 'max' => 255],
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
            'price_in' => 'Price IN',
            'price_out' => 'Price OUT',
            'number_start_with' => 'Num Start With',
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
        return $this->hasMany(Call::class, ['tariff_id' => 'id']);
    }

    /**
     * Gets query for [[LineTariffs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLineTariffs()
    {
        return $this->hasMany(LineTariff::class, ['default_call_tariff_id' => 'id']);
    }

    public function getLines()
    {
        return $this->hasMany(Line::class, ['id' => 'line_id'])->viaTable("call_tariff_to_line", ['call_tariff_id' => 'id']);
    }

    public static function getTariffByLineIdAndNumber(int $line_id, string $num, ?int $direction = null) : ?CallTariff
    {
        Yii::debug("Start tariff search $line_id $num");

        $num = preg_replace('/\D/', '', $num);
        $tariffs = self::find()
            ->joinWith(["lines"])
            ->where(['line_id' => $line_id])
            ->orderBy(["number_start_with" => SORT_DESC])
            ->all();
        foreach ($tariffs as $tariff) {
            if($tariff->number_start_with && str_starts_with($num, $tariff->number_start_with)) {
                return $tariff;
            }
        }
        return null;
    }

    public function getShortString() : string
    {
        return $this->name . " (o:".Yii::$app->formatter->asCurrency($this->price_out) ."/i:" . Yii::$app->formatter->asCurrency($this->price_in). "/" . CallTariffTypeEnum::tryFrom($this->type)?->name.")";
    }

}
