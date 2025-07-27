<?php

use common\models\Transaction;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var frontend\models\search\TransactionSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Transactions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transaction-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= \kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
//            'uuid',
            [
                'attribute' => 'sum',
                'value' => function (Transaction $transaction) {
                    $color = "success";
                    if($transaction->sum < 0) {
                        $color = "danger";
                    }
                    return "<span class='text-$color'>".Yii::$app->formatter->asCurrency($transaction->sum)."</span>";
                },
                'format' => 'raw',
            ],
            'description',
            //'status',
            [
                "attribute" => "created_at",
                'format' => 'datetime',
                'filterType' => \kartik\grid\GridView::FILTER_DATE_RANGE,
                'filterWidgetOptions' => [
                    'convertFormat'=>true,
                    'pluginOptions' => [
                        'opens'=>'right',
                        'locale' => [
                            'cancelLabel' => 'Clear',
                            'format' => 'Y-m-d',
                        ]
                    ]
                ]
            ],
            //'updated_at',
        ],
    ]); ?>


</div>
