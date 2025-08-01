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
        'floatPageSummary' => true,
        'showPageSummary' => true,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
//            'uuid',
            [
                'attribute' => 'sum',
                'value' => function (Transaction $transaction) {
//                    return $transaction->sum;
                    $color = "success";
                    if($transaction->sum < 0) {
                        $color = "danger";
                    }
                    return "<span class='text-$color'>".Yii::$app->formatter->asCurrency($transaction->sum)."</span>";
                },
                'format' => 'raw',
                'pageSummary' => function ($summary, $data, $widget) {
                        $amounts = [];
                        $amounts_plus = $amounts_minus = 0;

                        foreach ($data as $line) {
                            // Определим знак: debit или credit по классу
                            if (strpos($line, 'text-danger') !== false) {
                                $sign = -1;
                            } elseif (strpos($line, 'text-success') !== false) {
                                $sign = 1;
                            } else {
                                continue; // неизвестный формат, пропускаем
                            }

                            // Извлекаем числовое значение
                            if (preg_match('/\$([0-9,.]+)/', $line, $matches)) {
                                $amount = floatval(str_replace(',', '', $matches[1]));
                                $sum = $sign * $amount;
                                $amounts[] = $sum;
                                if($sign == -1) {
                                    $amounts_minus += $sum;
                                } else {
                                    $amounts_plus += $sum;
                                }
                            }
                        }

                        $str1 = "<span class='text-danger'>" . Yii::$app->formatter->asCurrency($amounts_minus * 100) . "</span>";
                        $str2 = "<span class='text-success'>" . Yii::$app->formatter->asCurrency($amounts_plus * 100) . "</span>";
                        $str = $str1.' | '.$str2 . " | ";
                        return $str . Yii::$app->formatter->asCurrency(array_sum($amounts) * 100) ;
                },
                "pageSummaryFunc" => \kartik\grid\GridView::F_COUNT
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
