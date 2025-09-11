<?php

namespace backend\controllers;
ini_set("memory_limit", "-1");

use common\models\ami\LiveCalls;
use common\models\Call;
use backend\models\search\CallSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CallController implements the CRUD actions for Call model.
 */
class CallController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
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

    /**
     * Lists all Call models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new CallSearch([
            "date_start" => Yii::$app->formatter->asDate('now', 'php:d-m-Y'),
            "date_end" => Yii::$app->formatter->asDate('now+1day', 'php:d-m-Y'),
        ]);
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->sort->defaultOrder = ['created_at' => SORT_DESC];
        $dataProvider->pagination->pageSize = 20;

        $totalSum = $totalProfit = $totalSec = 0;
        foreach ($dataProvider->query->all() as $model) {
            $totalSum += $model->sum;
            $totalSec += $model->getRealBillingDuration();
            $totalProfit += $model->getSum() - $model->getSumSupplier();
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'totalSum' => $totalSum,
            'totalProfit' => $totalProfit,
            'totalSec' => $totalSec,
        ]);
    }

    public function actionLive()
    {
        $liveCalls = new LiveCalls();
        $dataProvider = $liveCalls->search();

        return $this->render('live', [
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Displays a single Call model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Call model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Call();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Call model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Call model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionDownloadAudio($call_id, $name = null)
    {
        $call = Call::findOne(["call_id" => $call_id]);
        if(!$name) {
            $name = Yii::$app->security->generateRandomString();
        }
        return Yii::$app->response->sendFile($call->getRecordPath(), $name);
    }


    /**
     * Finds the Call model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Call the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Call::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
