<?php

use common\models\Line;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var frontend\models\search\LineSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Lines';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="line-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            'sip_num',
            [
                'attribute' => 'tariff_id',
                'value' => function (Line $model) {
                    return $model->tariff->name;
                },
            ],
        ],
    ]); ?>


</div>
