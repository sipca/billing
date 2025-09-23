<?php

namespace backend\controllers;

use common\enums\CallStatusEnum;
use common\models\AsteriskCdr;
use common\models\Call;
use common\models\CallTariff;
use common\models\Line;
use Yii;
use yii\rest\Controller;
use yii\web\Response;

class ApiController extends Controller
{
    public function beforeAction($action)
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        return parent::beforeAction($action);
    }

    public function actionCanCall($caller, $number)
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        $line = Line::findOne(["sip_num" => $caller]);
        
        if($line) {
            if($line->delay_sec) {
                $lastCall = Call::find()
                    ->where(["line_id" => $line->id, "direction" => Call::DIRECTION_OUT])
                    ->orderBy(["id" => SORT_DESC])
                    ->limit(1)
                    ->one();

                if($lastCall) {
                    $diff = time() - $lastCall->updated_at;
                    Yii::debug([
                        "updated_at" => $lastCall->updated_at,
                        'diff' => $diff
                    ]);

                    if($diff < $line->delay_sec) {
                        Yii::debug("DENY");
                        return "DENY";
                    }
                }
            }

            $users = $line->users;
            if($users) {
                foreach ($users as $user) {
                    if($user->canCall()) {
                        return "OK";
                    }
                }
                return "DENY";
            }
        }
        return "OK";
    }

    public function actionCallStart()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;

        $defaultStatus = CallStatusEnum::IN_PROGRESS->value;

        $line = Line::findOne(["sip_num" => Yii::$app->request->get('caller')]);

        $model = new Call([
            "call_id" => uniqid(),
            "line_id" => $line?->id,
            "tariff_id" => null,
            "source" => Yii::$app->request->get('caller'),
            "destination" => Yii::$app->request->get('number'),
            "record_link" => null,
            "duration" => null,
            "direction" => Yii::$app->request->get('direction'),
            "billing_duration" => null,
            "status" => $defaultStatus,
        ]);

        if($line) {
            if($tariff = CallTariff::getTariffByLineIdAndNumber($model->line_id, Yii::$app->request->get('number'))) {
                Yii::debug("Tariff found: $tariff->id");
                $model->tariff_id = $tariff->id;
            }
        }

        if($model->save()) {
            return $model->id;
        } else {
            Yii::debug($model->getErrors());
        }
        
        return 0;
    }

    public function actionCallEnd()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;

        $model = Call::findOne(Yii::$app->request->get('call_id'));

        if(!$model) {
            return "OK";
        }

        $model->billing_duration = Yii::$app->request->get('billsec');
        $model->status = CallStatusEnum::mapFromCdr(Yii::$app->request->get('status'))->value;
        $model->record_link = Yii::$app->request->get('recording');

        if(Yii::$app->request->get('tariff_id')) {
            $model->tariff_id = Yii::$app->request->get('tariff_id');
        }

        $model->save();

        if($user_id = Yii::$app->request->get('user_id')) {
            $model->charge((int) $user_id);
        } else {
            $model->charge();
        }

        return "OK";
    }

    public function actionCallAssign()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;

        $model = Call::findOne(Yii::$app->request->get('call_id'));
        $model->source = Yii::$app->request->get('operator');

        $line = Line::findOne(["sip_num" => Yii::$app->request->get('operator')]);
        if($line) {
            $model->line_id = $line->id;
            if($tariff = CallTariff::getTariffByLineIdAndNumber($model->line_id, $model->destination)) {
                Yii::debug("Tariff found: $tariff->id");
                $model->tariff_id = $tariff->id;
            }
            $model->assignTariff();
        }

        $model->save();

        return "OK";
    }
}