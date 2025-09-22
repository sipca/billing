<?php

use common\models\LineTariff;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Line $model */
/** @var yii\widgets\ActiveForm $form */

$tariffs = LineTariff::find()->select(["name", "id"])->indexBy("id")->column();

$formatter = new IntlDateFormatter(Yii::$app->formatter->locale, IntlDateFormatter::FULL, IntlDateFormatter::NONE, 'Europe/Moscow', IntlDateFormatter::GREGORIAN, 'EEEE');
$days = [];

for ($i = 1; $i <= 7; $i++) {
    $date = new DateTime("sunday +$i days");
    $days[$i] = $formatter->format($date);
}

?>

<div class="line-form">

    <?php $form = ActiveForm::begin([
        "fieldConfig" => [
            "options" => ["class" => "mb-3"],
        ]
    ]); ?>

    <div class="row">
        <div class="col-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-6">
            <?= $form->field($model, 'did_number')->textInput() ?>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <?= $form->field($model, 'sip_num')->textInput() ?>
        </div>
        <div class="col-6">
            <?= $form->field($model, 'password')->textInput() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-6">
            <?= $form->field($model, 'tariff_id')->widget(Select2::class, [
                "data" => $tariffs,
            ]) ?>
        </div>
        <div class="col-6">
            <?= $form->field($model, 'tariffs')->widget(Select2::class, [
                "data" => \common\models\CallTariff::find()->select(["name", "id"])->indexBy("id")->column(),
                "pluginOptions" => [
                    "allowClear" => true,
                    "multiple" => true
                ]
            ]) ?>
        </div>
    </div>


    <div class="row">
        <div class="col-6">
            <?= $form->field($model, 'pay_billing_day')->dropDownList($days) ?>
        </div>
        <div class="col-6">
            <?= $form->field($model, 'pay_date')->textInput() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-6">
            <?= $form->field($model, 'tolerance_billing_duration')->textInput() ?>
        </div>
        <div class="col-6">
            <?= $form->field($model, 'delay_sec')->textInput() ?>
        </div>
    </div>

    <?= $form->field($model, 'description')->textarea() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
