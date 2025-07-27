<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\CallTariff $model */

$this->title = Yii::t('app', 'Create Call Tariff');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Call Tariffs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="call-tariff-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
