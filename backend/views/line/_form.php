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

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'sip_num')->textInput() ?>
    <?= $form->field($model, 'password')->textInput() ?>
    <?= $form->field($model, 'did_number')->textInput() ?>

    <?= $form->field($model, 'tariff_id')->dropDownList($tariffs) ?>
    <?= $form->field($model, 'pay_billing_day')->dropDownList($days) ?>
    <?= $form->field($model, 'pay_date')->textInput() ?>
    <?= $form->field($model, 'tariffs')->widget(Select2::class, [
        "data" => \common\models\CallTariff::find()->select(["name", "id"])->indexBy("id")->column(),
        "pluginOptions" => [
            "allowClear" => true,
            "multiple" => true
        ]
    ]) ?>
    <?= $form->field($model, 'tolerance_billing_duration')->textInput() ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
