<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\User $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin([
            "fieldConfig" => [
                "options" => ["class" => "mb-3"],
            ]
    ]); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'password')->passwordInput() ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->dropDownList(\common\models\User::STATUSES) ?>
    <?= $form->field($model, 'credit_balance')->widget(\common\widgets\MoneyControl::class) ?>

    <?= $form->field($model, 'role')->dropDownList(\common\models\User::ROLES) ?>
    <?= $form->field($model, 'telegram_chat_id')->textInput() ?>

    <?php foreach (\common\models\Line::find()->all() as $line) { ?>
        <?=$form->field($model, "_lines[$line->id]")->checkbox(["label" => $line->name])?>
    <?php } ?>

    <div class="form-group mt-2">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
