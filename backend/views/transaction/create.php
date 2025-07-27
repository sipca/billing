<?php

/* @var $this \yii\web\View */
/* @var $model \common\models\Transaction */
$this->title = Yii::t("app", "Create Transaction");

$form = \yii\widgets\ActiveForm::begin([
    "fieldConfig" => [
        "options" => ["class" => "mb-3"],
    ]
]);

echo $form->field($model, "user_id")->dropDownList(\common\models\User::find()->select(["username", "id"])->indexBy('id')->column());
echo $form->field($model, "sum")->widget(\common\widgets\MoneyControl::class);
echo $form->field($model, "description")->textInput();
echo $form->field($model, "minus")->checkbox();
echo \yii\helpers\Html::submitButton(Yii::t("app", "Create Transaction"), ["class" => "btn btn-success"]);

\yii\widgets\ActiveForm::end();
?>

