<?php

use common\models\Call;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var frontend\models\search\CallSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Calls';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="call-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= \kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'call_id',
                'value' => function(\common\models\Call $model) {
                    return $model->call_id . " (" . $model->direction . ")";
                }
            ],
            [
                "attribute" => "line_id",
                "value" => function(\common\models\Call $model) {
                    return $model->line?->name;
                }
            ],
            "source",
            "destination",
            [
                "attribute" => "tariff_id",
                "value" => function(\common\models\Call $model) {
                    return $model->tariff?->getShortString();
                }
            ],
            [
                "attribute" => "billing_duration",
                "value" => function(\common\models\Call $model) {
                    return Yii::$app->formatter->asDuration($model->billing_duration) . " (" . Yii::$app->formatter->asCurrency($model->getSum()) .")";
                }
            ],
            [
                "attribute" => "status",
                "value" => function(\common\models\Call $model) {
                    return \common\enums\CallStatusEnum::tryFrom($model->status)?->name;
                },
                'filter' => \common\enums\CallStatusEnum::array(),
            ],
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
            [
                'attribute' => 'record_link',
                'value' => function(\common\models\Call $model) {
                    return $model->getRecord();
                },
                'format' => 'raw',
            ]
        ]
    ]); ?>


</div>
