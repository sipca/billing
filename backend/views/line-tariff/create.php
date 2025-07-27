<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\LineTariff $model */

$this->title = Yii::t('app', 'Create Line Tariff');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Line Tariffs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="line-tariff-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
