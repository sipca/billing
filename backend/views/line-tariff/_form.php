<?php

use common\enums\LineTariffEnum;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\LineTariff $model */
/** @var yii\widgets\ActiveForm $form */

$callTariffs = \common\models\CallTariff::find()->select(["name", "id"])->indexBy('id')->column();
?>

<div class="line-tariff-form">

    <?php $form = ActiveForm::begin([
        "fieldConfig" => [
            "options" => ["class" => "mb-3"],
        ]
    ]); ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->dropDownList(LineTariffEnum::array()) ?>

    <?= $form->field($model, 'default_call_tariff_id')->dropDownList($callTariffs) ?>

    <?= $form->field($model, 'price')->widget(\common\widgets\MoneyControl::class) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
