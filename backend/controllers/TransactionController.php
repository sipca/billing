<?php

namespace backend\controllers;

use common\enums\TransactionStatusEnum;
use common\enums\TransactionTypeEnum;
use common\models\Transaction;
use jp3cki\uuid\Uuid;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

class TransactionController extends Controller
{
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                    ],
                ],
            ]
        );
    }

    public function actionCreate($user_id = null)
    {
        $model = new Transaction([
            'user_id' => $user_id,
        ]);

        if($model->load(Yii::$app->request->post())) {
            $transaction = Transaction::create($model->user_id, TransactionTypeEnum::MANUAL, $model->sum *= $model->minus ? -100 : 100, $model->description, TransactionStatusEnum::PAID);
            $balance = Yii::$app->formatter->asCurrency($transaction->user->balance);
            $sum = Yii::$app->formatter->asCurrency($transaction->sum);
            $text = "👤 <b>{$transaction->user->username}</b>" . PHP_EOL . PHP_EOL;
            $text .= "❗️The balance has been replenished ($sum)" . PHP_EOL;
            $text .= "<b>💰 Actual balance: $balance</b>";

            $transaction->user->sendMessageInTelegram($text);
            return $this->redirect(["user/view", "id" => $model->user_id]);
        }

        return $this->render('create', compact('model'));
    }

    public function actionDeleteFromUser($id)
    {
        $model = Transaction::findOne($id);

        if($model) {
            $model->delete();
        }

        return $this->redirect($this->request->referrer);
    }

}