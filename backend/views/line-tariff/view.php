<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\LineTariff $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Line Tariffs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="line-tariff-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            [
                'attribute' => 'type',
                'value' => function ($model) {
                    return \common\enums\LineTariffEnum::tryFrom($model->type)->name;
                }
            ],
            [
                'attribute' => 'default_call_tariff_id',
                'value' => function (\common\models\LineTariff $lineTariff) {
                    return $lineTariff->defaultCallTariff->getShortString();
                }
            ],
            'price:currency',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
