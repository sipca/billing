<?php

/** @var yii\web\View $this */
/** @var array $summary */
/** @var array $summaryTfByMinutes */

$this->title = 'Admin Panel';
?>
<script type="application/json" id="summaryTfByMinutes">
    <?=json_encode($summaryTfByMinutes)?>
</script>
<div class="site-index">
    <div class="row">
        <div class="col-8">
            <div class="card">
                <div class="card-header">Today stats</div>
                <div class="card-body">
                    <?php if($summary) { ?>
                    <div class="table">
                        <table class="table table-bordered">
                            <tr>
                                <th>USER</th>
                                <th>Tariffs</th>
                                <th>IN</th>
                                <th>OUT</th>
                                <th>TOTAL</th>
                            </tr>

                            <?php foreach ($summary as $user => $tariffs) { ?>
                                <?php $rowspan = count($tariffs); $first = true; ?>
                                <?php foreach ($tariffs as $tariff) { ?>
                                    <tr>
                                        <?php if ($first) { ?>
                                            <td rowspan="<?= $rowspan ?>"><?= htmlspecialchars($user) ?></td>
                                            <?php $first = false; ?>
                                        <?php } ?>
                                        <td><?= htmlspecialchars($tariff['name']) ?></td>
                                        <td><?=$tariff["total_in_calls_count"]?> / <?=Yii::$app->formatter->asDuration($tariff["total_in_calls_duration"])?> / <?=Yii::$app->formatter->asCurrency($tariff["total_in_spent"])?></td>
                                        <td><?=$tariff["total_out_calls_count"]?> / <?=Yii::$app->formatter->asDuration($tariff["total_out_calls_duration"])?> / <?=Yii::$app->formatter->asCurrency($tariff["total_out_spent"])?></td>
                                        <td><?=Yii::$app->formatter->asCurrency($tariff["total_spent"])?> (<?=Yii::$app->formatter->asCurrency($tariff["profit"])?>)</td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                        </table>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card">
                <div class="card-header">Charts</div>
                <div class="card-body">
                    <canvas id="chart-pie-by-minutes"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
