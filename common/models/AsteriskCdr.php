<?php

namespace common\models;

use common\enums\CallStatusEnum;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "cdr".
 *
 * @property string $calldate
 * @property string $clid
 * @property string $src
 * @property string $dst
 * @property string $dcontext
 * @property string $channel
 * @property string $dstchannel
 * @property string $lastapp
 * @property string $lastdata
 * @property int $duration
 * @property int $billsec
 * @property string $disposition
 * @property int $amaflags
 * @property string $accountcode
 * @property string $uniqueid
 * @property string $userfield
 * @property string $did
 * @property string $recordingfile
 * @property string $cnum
 * @property string $cnam
 * @property string $outbound_cnum
 * @property string $outbound_cnam
 * @property string $dst_cnam
 * @property string $linkedid
 * @property string $peeraccount
 * @property int $sequence
 */
class AsteriskCdr extends ActiveRecord
{
//    public static function getDb()
//    {
//        return Yii::$app->asteriskdb;
//    }

    public static function tableName()
    {
        return 'cdr';
    }

    public function rules()
    {
        return [
            [['calldate'], 'safe'],
            [['duration', 'billsec', 'amaflags', 'sequence'], 'integer'],
            [['clid', 'src', 'dst', 'dcontext', 'channel', 'dstchannel', 'lastapp', 'lastdata', 'disposition', 'accountcode', 'uniqueid', 'userfield', 'did', 'recordingfile', 'cnum', 'cnam', 'outbound_cnum', 'outbound_cnam', 'dst_cnam', 'linkedid', 'peeraccount'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'calldate' => Yii::t('app', 'Call Date'),
            'clid' => Yii::t('app', 'Caller ID'),
            'src' => Yii::t('app', 'Source'),
            'dst' => Yii::t('app', 'Destination'),
            'dcontext' => Yii::t('app', 'Dial Context'),
            'channel' => Yii::t('app', 'Channel'),
            'dstchannel' => Yii::t('app', 'Destination Channel'),
            'lastapp' => Yii::t('app', 'Last Application'),
            'lastdata' => Yii::t('app', 'Last Data'),
            'duration' => Yii::t('app', 'Duration'),
            'billsec' => Yii::t('app', 'Billable Seconds'),
            'disposition' => Yii::t('app', 'Disposition'),
            'amaflags' => Yii::t('app', 'AMA Flags'),
            'accountcode' => Yii::t('app', 'Account Code'),
            'uniqueid' => Yii::t('app', 'Unique ID'),
            'userfield' => Yii::t('app', 'User Field'),
            'did' => Yii::t('app', 'DID'),
            'recordingfile' => Yii::t('app', 'Recording File'),
            'cnum' => Yii::t('app', 'Caller Number'),
            'cnam' => Yii::t('app', 'Caller Name'),
            'outbound_cnum' => Yii::t('app', 'Outbound Caller Number'),
            'outbound_cnam' => Yii::t('app', 'Outbound Caller Name'),
            'dst_cnam' => Yii::t('app', 'Destination Caller Name'),
            'linkedid' => Yii::t('app', 'Linked ID'),
            'peeraccount' => Yii::t('app', 'Peer Account'),
            'sequence' => Yii::t('app', 'Sequence'),
        ];
    }

    public static function importAll($limit) : bool
    {
        $calls = self::find()
            ->limit($limit)
            ->orderBy(['calldate' => SORT_DESC])
            ->all();

        if(!$calls) {
            return false;
        }

        foreach($calls as $call) {
            $call->import();
        }

        return true;
    }

    public function import() : bool
    {
        Yii::debug("Start import");
        $model = new Call([
            "call_id" => $this->uniqueid,
            "line_id" => $this->getLineId(),
            "tariff_id" => null,
            "source" => $this->src,
            "destination" => $this->dst,
            "record_link" => $this->recordingfile,
            "duration" => $this->duration,
            "direction" => $this->getDirection(),
            "billing_duration" =>  $this->billsec,
            "status" => CallStatusEnum::mapFromCdr($this->disposition)->value,
        ]);

        if($model->line_id) {
            if($tariff = CallTariff::getTariffByLineIdAndNumber($model->line_id, $this->getDirection() === Call::DIRECTION_OUT ? $model->destination : $model->source)) {
                Yii::debug("Tariff found: $tariff->id");
                $model->tariff_id = $tariff->id;
            }
        }

        if($model->save()) {
            $model->updateAttributes(["created_at" => Yii::$app->formatter->asTimestamp($this->calldate)]);

            return true;
        }

        Yii::debug($model->getErrors());
        return false;
    }

    public function getLineId() : int|null
    {
        $num = $this->dst;

        if($this->dcontext === env("OUTBOUND_CONTEXT")) {
            $num = $this->cnum;
        }

        if($line = Line::findOne(["sip_num" => $num])) {
            return $line->id;
        }

        return null;
    }

    public function getDirection() : string
    {
        return $this->dcontext === env("OUTBOUND_CONTEXT") ? Call::DIRECTION_OUT : Call::DIRECTION_IN;
    }

}
