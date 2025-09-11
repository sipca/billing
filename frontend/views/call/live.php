<?php

use common\models\Call;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ArrayDataProvider $dataProvider */

$this->title = 'Live calls';
$this->params['breadcrumbs'][] = $this->title;

$js = <<<JS
setInterval(function () {
        $.pjax.reload({
            container: '#w0', // ID контейнера Pjax
            async: false                     // можно убрать, если не нужно блокировать
        });
    }, 2000); // интервал 2000 мс = 2 секунды
JS;

$this->registerJs($js);
?>
<div class="call-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php \yii\widgets\Pjax::begin([
        'enablePushState' => false,
        'formSelector' => false,
        'linkSelector' => false,
    ]) ?>

    <?= \kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'line_id',
                'value' => 'line.name',
            ],
            [
                'attribute' => 'destination',
                'value' => function (Call $model) {
                    $client = \common\models\ClientNumber::find()
                        ->where(["like", "number", $model->destination])
                        ->andWhere(["created_by" => Yii::$app->user->id])
                        ->one();
                    if($client) {
                        return $client->number . " - " . $client->name;
                    }
                    return $model->destination;
                },
            ]
        ]
    ]); ?>

    <?php Pjax::end() ?>


</div>
