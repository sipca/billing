<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\CallTariff $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="call-tariff-form">

    <?php $form = ActiveForm::begin([
        "fieldConfig" => [
            "options" => ["class" => "mb-3"],
        ]
    ]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->dropDownList(\common\enums\CallTariffTypeEnum::array()) ?>

    <?= $form->field($model, 'price_in')->widget(\common\widgets\MoneyControl::class) ?>
    <?= $form->field($model, 'price_out')->widget(\common\widgets\MoneyControl::class) ?>

    <?= $form->field($model, 'number_start_with')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
