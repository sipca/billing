<?php

namespace console\controllers;

use common\components\ami\actions\PjsipShowContactsAction;
use common\models\ami\LiveCalls;
use PAMI\Message\Action\BridgeInfoAction;
use Yii;
use yii\console\Controller;

class AsteriskController extends Controller
{
    public function actionIndex()
    {
        Yii::$app->ami->open();
        $response = Yii::$app->ami->send(new BridgeInfoAction("3d577972-9086-4461-a008-4846e4f5173e"));
        $events = $response->getEvents();
        print_r($events);
        echo "\n";
    }
}