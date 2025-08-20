<?php

namespace backend\controllers;

use common\models\LoginForm;
use common\models\User;
use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $users = User::find()
            ->where(["role" => User::ROLE_USER])
            ->all();

        $summary = $summaryTfByMinutes = [];

        foreach ($users as $user) {
            $data = $user->summaryByPeriod(
                Yii::$app->formatter->asTimestamp("now 00:00:00"),
                Yii::$app->formatter->asTimestamp("now 00:00:00 +1day")
            );

            if($data) {
                $summary[$user->username] = $data["byTariffs"];
                foreach ($data["byTariffs"] as $tf_id => $tariff_data) {
                    if(!isset($summaryTfByMinutes[$tariff_data["name"]])) {
                        $summaryTfByMinutes[$tariff_data["name"]] = 0;
                    }

                    $summaryTfByMinutes[$tariff_data["name"]] += $tariff_data["total_in_calls_duration"] + $tariff_data["total_out_calls_duration"];
                }
            }
        }

        return $this->render('index', compact('summary', 'summaryTfByMinutes'));
    }

    /**
     * Login action.
     *
     * @return string|Response
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'blank';

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post())) {
            if($model->loginAsAdmin()) {
                return $this->goBack();
            }
            return $this->goBack();
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
