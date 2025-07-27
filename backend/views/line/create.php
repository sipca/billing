<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Line $model */

$this->title = Yii::t('app', 'Create Line');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Lines'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="line-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
