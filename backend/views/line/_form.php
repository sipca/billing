<?php

use common\models\LineTariff;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Line $model */
/** @var yii\widgets\ActiveForm $form */

$tariffs = LineTariff::find()->select(["name", "id"])->indexBy("id")->column();
?>

<div class="line-form">

    <?php $form = ActiveForm::begin([
        "fieldConfig" => [
            "options" => ["class" => "mb-3"],
        ]
    ]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'sip_num')->textInput() ?>

    <?= $form->field($model, 'tariff_id')->dropDownList($tariffs) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
