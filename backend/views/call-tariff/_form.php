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

    <div class="row">
        <div class="col-6">
            <?= $form->field($model, 'type')->dropDownList(\common\enums\CallTariffTypeEnum::array()) ?>
        </div>
        <div class="col-6">
            <?= $form->field($model, 'supplier_type')->dropDownList(\common\enums\CallTariffTypeEnum::array()) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <?= $form->field($model, 'price_in')->widget(\common\widgets\MoneyControl::class) ?>
        </div>
        <div class="col-6">
            <?= $form->field($model, 'supplier_price_in')->widget(\common\widgets\MoneyControl::class) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <?= $form->field($model, 'price_out')->widget(\common\widgets\MoneyControl::class) ?>
        </div>
        <div class="col-6">
            <?= $form->field($model, 'supplier_price_out')->widget(\common\widgets\MoneyControl::class) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <?= $form->field($model, 'price_connection_in')->widget(\common\widgets\MoneyControl::class) ?>
        </div>
        <div class="col-6">
            <?= $form->field($model, 'supplier_connection_price_in')->widget(\common\widgets\MoneyControl::class) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-6">
            <?= $form->field($model, 'price_connection_out')->widget(\common\widgets\MoneyControl::class) ?>
        </div>
        <div class="col-6">
            <?= $form->field($model, 'supplier_connection_price_out')->widget(\common\widgets\MoneyControl::class) ?>
        </div>
    </div>

    <?= $form->field($model, 'number_start_with')->widget(\kartik\select2\Select2::class, [
            "pluginOptions" => [
                "allowClear" => true,
                "tags" => true,
                "multiple" => true,
            ],
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
