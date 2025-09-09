<?php

use common\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\search\UserSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create User'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'username',
//            'auth_key',
//            'access_token',
//            'password_hash',
            'email:email',
            [
                'attribute' => 'balance',
                'value' => function(\common\models\User $model){
                    $class = "success";
                    if($model->balance < 0) {
                        $class = "danger";
                    }
                    $balance = Yii::$app->formatter->asCurrency($model->balance);
                    return "<span class='text-$class'>$balance</span>";
                },
                'format' => 'html',
            ],
            'credit_balance:currency',
            'status',
            'role',
            'created_at:datetime',
            //'updated_at',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, User $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 },
                "visibleButtons" => [
                    "view" => true,
                    "update" => true,
                    "delete" => false,
                ]
            ],
        ],
    ]); ?>


</div>
