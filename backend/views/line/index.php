<?php

use common\models\Line;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\search\LineSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Lines');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="line-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Line'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            'sip_num',
            [
                'attribute' => 'tariff_id',
                'value' => function (Line $line) {
                    return $line->tariff->name;
                },
                'filter' => \common\models\LineTariff::find()->select(['name', 'id'])->indexBy('id')->column(),
            ],
            [
                'attribute' => 'pay_billing_day',
                'value' => 'payBillingDayText',
                'filter' => \common\enums\WeekDayEnum::array()
            ],
            [
                'attribute' => 'pay_date',
                "visible" => Yii::$app->user->identity->isAdmin()
            ],
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Line $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
