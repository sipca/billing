<?php

use common\models\LineTariff;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\search\LineTariffSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Line Tariffs');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="line-tariff-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Line Tariff'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            [
                'attribute' => 'type',
                'value' => function ($model) {
                    return \common\enums\LineTariffEnum::tryFrom($model->type)->name;
                },
                'filter' => \common\enums\LineTariffEnum::array()
            ],
            [
                'attribute' => 'default_call_tariff_id',
                'value' => function (LineTariff $model) {
                    return $model->defaultCallTariff->getShortString();
                },
                'filter' => \common\models\CallTariff::find()->select([ 'name', 'id'])->indexBy('id')->column(),
            ],
            'price:currency',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, LineTariff $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
