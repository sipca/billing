<?php

namespace backend\controllers;

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

    public function actionCallStart($caller, $number, $trunk, $channel, $direction)
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::debug(Yii::$app->request->queryParams);
        
        return 555;
    }

    public function actionCallEnd($call_id, $caller, $number, $trunk, $channel, $direction, $answered, $status, $recording)
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::debug(Yii::$app->request->queryParams);

        return "OK";
    }
}