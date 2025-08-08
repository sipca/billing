<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var \frontend\models\DialerForm $model */

$this->title = "Dialer";
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="call-view">

    <h1><?= Html::encode($this->title) ?></h1>


    <?php $form = ActiveForm::begin([
        "fieldConfig" => [
            "options" => ["class" => "mb-3"],
        ]
    ]); ?>
    <?=$form->field($model, "numbers")->textarea(["rows" => 5])?>
    <?=$form->field($model, "lines")->widget(Select2::class, [
        "data" => \common\models\Line::find()->joinWith('users')->where(["user.id" => Yii::$app->user->id])->select(["line.name", "line.id"])->indexBy("id")->column(),
        "pluginOptions" => [
            "allowClear" => true,
            "multiple" => true
        ]
    ]) ?>

    <?=Html::submitButton("Start", ["class" => "btn btn-success"])?>
    <?php \yii\widgets\ActiveForm::end() ?>

</div>
