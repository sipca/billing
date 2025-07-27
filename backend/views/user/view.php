<?php

use common\models\Transaction;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\User $model */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'username',
            'email:email',
            'balance:currency',
            'status',
            'role',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

    <?=Html::a("Add", ["transaction/create", "user_id" => $model->id],["class" => "btn btn-success"])?>
    <?=\kartik\grid\GridView::widget([
        "dataProvider" => new ActiveDataProvider([
            'query' => $model->getTransactions()->orderBy(["id" => SORT_DESC]),
        ]),
        "columns" => [
            "uuid",
            [
                "attribute" => "sum",
                'value' => function (Transaction $transaction) {
                    $color = "success";
                    if($transaction->sum < 0) {
                        $color = "danger";
                    }
                    return "<span class='text-$color'>".Yii::$app->formatter->asCurrency($transaction->sum)."</span>";
                },
                'format' => 'raw',
            ],
            "description",
            "created_at:datetime",
            [
                "class" => "yii\grid\ActionColumn",
                "template" => "{delete}",
                "urlCreator" => function ($action, $model, $key, $index) {
                    if($action == "delete") {
                        return \yii\helpers\Url::toRoute(["transaction/delete-from-user", "id" => $model->id]);
                    }
                }
            ]
        ]
    ])?>
</div>
