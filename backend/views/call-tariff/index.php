<?php

use common\enums\CallTariffTypeEnum;
use common\models\CallTariff;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\search\CallTariffSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Call Tariffs');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="call-tariff-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Call Tariff'), ['create'], ['class' => 'btn btn-success']) ?>
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
                'value' => function (CallTariff $model) {
                    return \common\enums\CallTariffTypeEnum::tryFrom($model->type)?->name;
                },
                'filter' => CallTariffTypeEnum::array()
            ],
            'price_in:currency',
            'price_out:currency',
            [
                "attribute" => "number_start_with",
                "value" => function (CallTariff $model) {
                    return implode(", ", $model->number_start_with);
                },
            ],
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, CallTariff $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
