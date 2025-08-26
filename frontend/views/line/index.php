<?php

use common\models\Line;
use rmrevin\yii\fontawesome\FAS;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var frontend\models\search\LineSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $events */

$this->title = 'Lines';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="line-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
            ],
            [
                'attribute' => 'name',
                'value' => function ($data) use ($events) {
                    $icon = FAS::i(FAS::_CIRCLE, [
                        "class" => $data->getConnectionInfo($events) ? "text-success" : "text-danger",
                    ]);
                    return "$icon " . $data->name;
                },
                "format" => "raw",
            ],
            'sip_num',
            'password',
            'did_number',
            [
                'attribute' => 'pay_billing_day',
                'value' => 'payBillingDayText'
            ],
            [
                'attribute' => 'tariff_id',
                'value' => function (Line $model) {
                    return $model->tariff->name;
                },
            ],
        ],
    ]); ?>


</div>
