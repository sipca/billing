<?php

namespace console\controllers;
ini_set("memory_limit", "-1");

use common\models\AsteriskCdr;
use common\models\CallTariff;
use yii\console\Controller;

class ImportController extends Controller
{
    public function actionIndex($limit = 2000)
    {
        AsteriskCdr::importAll($limit);
    }

    public function actionTest($num)
    {
        print_r(CallTariff::getTariffByNumber($num));
    }
}