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
 * @property int|null $price_connection_in
 * @property int|null $price_connection_out
 * @property int|null $price_in
 * @property int|null $price_out
 * @property int|null $supplier_price_in
 * @property int|null $supplier_price_out
 * @property int|null $supplier_connection_price_in
 * @property int|null $supplier_connection_price_out
 * @property int|null $supplier_type
 * @property string|null $number_start_with
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Call[] $calls
 * @property LineTariff[] $lineTariffs
 * @property Line[] $lines
 * @property NumberPrefix[] $prefixes
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
            [['type', 'number_start_with'], 'default', 'value' => null],
            [['price_in','price_out', 'price_connection_in', 'price_connection_out','supplier_connection_price_out', 'supplier_connection_price_in','supplier_price_out', 'supplier_price_in'], 'default', 'value' => 0],
            [['name'], 'required'],
            [['type', 'created_at', 'updated_at', 'supplier_type'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [["number_start_with"], "safe"],
            [[
                'price_in','price_out', 'price_connection_in', 'price_connection_out',
                'supplier_connection_price_out', 'supplier_connection_price_in',
                'supplier_price_out', 'supplier_price_in'
                ], "number"]
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
            'number_start_with' => 'Number prefixes',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'supplier_type' => 'Supplier Type',
            'supplier_price_out' => 'Supplier Price OUT',
            'supplier_price_in' => 'Supplier Price IN',
            'supplier_connection_price_out' => 'Supplier Connection Price OUT',
            'supplier_connection_price_in' => 'Supplier Connection Price IN',
            'connection_price_out' => 'Connection Price OUT',
            'connection_price_in' => 'Connection Price IN',
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

    public function getPrefixes()
    {
        return $this->hasMany(NumberPrefix::class, ["call_tariff_id" => "id"]);
    }

    public static function getTariffByLineIdAndNumber(int $line_id, string $num, ?int $direction = null) : ?CallTariff
    {
        Yii::debug("Start tariff search $line_id $num");

        $num = preg_replace('/\D/', '', $num);
        $tariffs = self::find()
            ->joinWith(["lines", 'prefixes'])
            ->where(['line_id' => $line_id])
            ->orderBy(["prefix" => SORT_DESC])
            ->all();
        foreach ($tariffs as $tariff) {
            if($tariff->prefixes) {
                foreach ($tariff->prefixes as $prefix) {
                    if($prefix->prefix && str_starts_with($num, $prefix->prefix)) {
                        return $tariff;
                    }
                }
            }
        }
        return null;
    }

    public function beforeSave($insert)
    {
        NumberPrefix::deleteAll(['call_tariff_id' => $this->id]);

        if($this->number_start_with) {
            foreach ($this->number_start_with as $prefix) {
                $model = new NumberPrefix([
                    "call_tariff_id" => $this->id,
                    "prefix" => $prefix,
                ]);
                $model->save();
            }
            $this->number_start_with = '';
        }
        return parent::beforeSave($insert);
    }

    public function getShortString() : string
    {
        return $this->name . " (o:".Yii::$app->formatter->asCurrency($this->price_out) ."/i:" . Yii::$app->formatter->asCurrency($this->price_in). "/" . CallTariffTypeEnum::tryFrom($this->type)?->name.")";
    }

    public function convertPrices($reverse = false) : void
    {
        if(!$reverse) {
            $this->price_out = $this->price_out ? $this->price_out*100 : 0;
            $this->price_in = $this->price_in? $this->price_in*100 : 0;
            $this->supplier_price_in = $this->supplier_price_in ? $this->supplier_price_in*100 : 0;
            $this->supplier_price_out = $this->supplier_price_out ? $this->supplier_price_out* 100 : 0;
            $this->supplier_connection_price_in = $this->supplier_connection_price_in ? $this->supplier_connection_price_in*100 : 0;
            $this->supplier_connection_price_out = $this->supplier_connection_price_out? $this->supplier_connection_price_out* 100 :0;
            $this->price_connection_out = $this->price_connection_out? $this->price_connection_out*100:0;
            $this->price_connection_in = $this->price_connection_in?$this->price_connection_in*100:0;
        } else {
            $this->price_out /= 100;
            $this->price_in /= 100;
            $this->supplier_price_in /= 100;
            $this->supplier_price_out /= 100;
            $this->supplier_connection_price_in /= 100;
            $this->supplier_connection_price_out /= 100;
            $this->price_connection_out /= 100;
            $this->price_connection_in /= 100;
        }
    }

    public function afterFind()
    {
        parent::afterFind();
        $this->number_start_with = NumberPrefix::find()
            ->select(["prefix"])
            ->where(["call_tariff_id" => $this->id])
            ->column();
    }

}
