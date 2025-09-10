<?php

/** @var yii\web\View $this */
/** @var \yii\data\ActiveDataProvider $callsDataProvider */
/** @var array $byTariffs */

$this->title = Yii::t("app", "Home");
?>
<div class="site-index">
    <div class="row">
        <div class="col-6">
            <div class="card">
                <div class="card-header"><span style="font-size: 30px;">Balance</span></div>
                <div class="card-body">
                    <div class="text-center">
                        <span class="<?=Yii::$app->user?->identity?->balance > 0? "text-success" : "text-danger" ?>" style="font-size: 60px;"><?=Yii::$app->formatter->asCurrency(Yii::$app->user?->identity?->balance)?></span><br/>
<!--                        <span class="text-muted" style="font-size: 20px;">Available credit: 10$</span>-->
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card">
                <div class="card-header"><span style="font-size: 30px;">Today</span></div>
                <div class="card-body">
                    <table class="table table-bordered w-100">
                        <tr>
                            <th width="150"></th>
                            <th>Inbound calls</th>
                            <th>Outbound calls</th>
                            <th>Total spent</th>
                        </tr>
                        <?php foreach ($byTariffs as $tariff) { ?>
                        <tr>
                            <th><?=$tariff["name"]?></th>
                            <td><?=$tariff["total_in_calls_count"]?> / <?=Yii::$app->formatter->asDuration($tariff["total_in_calls_duration"])?> / <?=Yii::$app->formatter->asCurrency($tariff["total_in_spent"])?></td>
                            <td><?=$tariff["total_out_calls_count"]?> / <?=Yii::$app->formatter->asDuration($tariff["total_out_calls_duration"])?> / <?=Yii::$app->formatter->asCurrency($tariff["total_out_spent"])?></td>
                            <td><?=Yii::$app->formatter->asCurrency($tariff["total_spent"])?></td>
                        </tr>
                        <?php } ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-12">
            <div class="card">
                <div class="card-header">Today calls</div>
                <div class="card-body">
                    <?=\yii\grid\GridView::widget([
                        "dataProvider" => $callsDataProvider,
                        "columns" => [
                            [
                                'attribute' => 'call_id',
                                'value' => function(\common\models\Call $model) {
                                    return $model->call_id . " (" . $model->direction . ")";
                                }
                            ],
                            [
                                "attribute" => "line_id",
                                "value" => function(\common\models\Call $model) {
                                    return $model->line?->name;
                                },
                            ],
                            "source",
                            "destination",
                            [
                                "attribute" => "tariff_id",
                                "value" => function(\common\models\Call $model) {
                                    return $model->tariff?->getShortString();
                                }
                            ],
                            [
                                "attribute" => "billing_duration",
                                "value" => function(\common\models\Call $model) {
                                    return Yii::$app->formatter->asDuration((int) $model->billing_duration) . " (" . Yii::$app->formatter->asCurrency($model->getSum()) .")";
                                }
                            ],
                            [
                                "attribute" => "status",
                                "value" => function(\common\models\Call $model) {
                                    return \common\enums\CallStatusEnum::tryFrom($model->status)?->name;
                                },
                            ],
                            "created_at:datetime",
                            [
                                'attribute' => 'record_link',
                                'value' => function(\common\models\Call $model) {
                                    return $model->getRecord();
                                },
                                'format' => 'raw',
                            ]
                        ]
                    ])?>
                </div>
            </div>
        </div>
    </div>
</div>
