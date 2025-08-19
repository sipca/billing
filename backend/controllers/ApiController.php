<?php

namespace backend\controllers;

use common\models\Line;
use Yii;
use yii\rest\Controller;
use yii\web\Response;

class ApiController extends Controller
{
    public function actionCanCall($caller, $number)
    {
        $line = Line::findOne(["sip_num" => $caller]);
        Yii::$app->response->format = Response::FORMAT_RAW;
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
}