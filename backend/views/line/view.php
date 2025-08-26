<?php

use rmrevin\yii\fontawesome\FAS;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Line $model */
/* @var $events \PAMI\Message\Event\EventMessage[] */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Lines'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$connectionInfo = $model->getConnectionInfo($events);
$icon = FAS::i(FAS::_CIRCLE, [
    "class" => $connectionInfo ? "text-success" : "text-danger",
]);
?>
<div class="line-view">

    <h1><?= Html::encode($this->title) ?> <?=$icon?></h1>

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
            'description',
            'sip_num',
            'did_number',
            'password',
            [
                'attribute' => 'tariff_id',
                'label' => 'Tariff name',
                'value' => function ($model) {
                    $str = $model->tariff->name;

                    if($model->tariff->default_call_tariff_id) {
                        $str .= " - " . $model->tariff->defaultCallTariff->getShortString();
                    }
                    return $str;
                },
            ],
            'payBillingDayText',
            'pay_date',
            'tolerance_billing_duration',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

    <?=$connectionInfo ? DetailView::widget([
        "model" => $connectionInfo
    ]) : ""?>

</div>
