<?php

namespace console\controllers;

use common\enums\LineTariffEnum;
use common\models\Line;
use common\models\User;
use yii\console\Controller;

class BillingController extends Controller
{
    public function actionDay()
    {
        $lines = Line::find()
            ->joinWith('tariff')
            ->where(["line_tariff.type" => LineTariffEnum::DAILY->value])
            ->all();

        if(!$lines) return true;

        foreach ($lines as $line) {
            $line->addTransactionToUsers();
        }

        return true;
    }

    public function actionWeek()
    {
        $lines = Line::find()
            ->joinWith('tariff')
            ->where(["line_tariff.type" => LineTariffEnum::WEEKLY->value])
            ->andWhere(["pay_billing_day" => date('N')])
            ->all();

        if(!$lines) return true;

        foreach ($lines as $line) {
            $line->addTransactionToUsers();
        }

        return true;
    }

    public function actionMonth()
    {
        $lines = Line::find()
            ->joinWith('tariff')
            ->where(["line_tariff.type" => LineTariffEnum::MONTHLY->value])
            ->andWhere(["pay_date" => date('j')])
            ->all();

        if(!$lines) return true;

        foreach ($lines as $line) {
            $line->addTransactionToUsers();
        }

        return true;
    }

    public function actionNotify()
    {
        $users = User::find()
            ->all();
        foreach ($users as $user) {
            $balance = \Yii::$app->formatter->asCurrency($user->balance);
            $text = "👤 <b>$user->username</b>" . PHP_EOL . PHP_EOL;
            $text .= "💰Your actual balance is: <b>$balance</b>";
            $user->sendMessageInTelegram($text);
        }
    }
}