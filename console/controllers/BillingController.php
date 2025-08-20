<?php

namespace console\controllers;

use common\enums\LineTariffEnum;
use common\models\Line;
use common\models\User;
use SebastianBergmann\CodeCoverage\Report\PHP;
use Yii;
use yii\console\Controller;

class BillingController extends Controller
{
    public function actionDay()
    {
        $lines = Line::find()
            ->joinWith('tariff')
            ->where(["line_tariff.type" => LineTariffEnum::DAILY->value])
            ->all();

        if(!$lines) return true;

        foreach ($lines as $line) {
            $line->addTransactionToUsers();
        }

        return true;
    }

    public function actionWeek()
    {
        $lines = Line::find()
            ->joinWith('tariff')
            ->where(["line_tariff.type" => LineTariffEnum::WEEKLY->value])
            ->andWhere(["pay_billing_day" => date('N')])
            ->all();

        if(!$lines) return true;

        foreach ($lines as $line) {
            $line->addTransactionToUsers();
        }

        return true;
    }

    public function actionMonth()
    {
        $lines = Line::find()
            ->joinWith('tariff')
            ->where(["line_tariff.type" => LineTariffEnum::MONTHLY->value])
            ->andWhere(["pay_date" => date('j')])
            ->all();

        if(!$lines) return true;

        foreach ($lines as $line) {
            $line->addTransactionToUsers();
        }

        return true;
    }

    public function actionNotify()
    {
        $users = User::find()
            ->where(["is not", "telegram_chat_id", null])
            ->all();
        foreach ($users as $user) {
            if(!$user->telegram_chat_id) continue;

            $balance = Yii::$app->formatter->asCurrency($user->balance);
            $text = "👤 <b>$user->username</b>" . PHP_EOL . PHP_EOL;
            if($user->balance < 5000) {
                $text .= "❗️❗";
            }
            $text .= "💰Balance: <b>$balance</b>";
            $user->sendMessageInTelegram($text);
        }
    }

    public function actionDayNotifier()
    {
        $users = User::find()
            ->where(["is not", "telegram_chat_id", null])
            ->all();

        foreach ($users as $user) {
            if (!$user->telegram_chat_id) {
                continue;
            }

            $data = $user->summaryByPeriod(
                Yii::$app->formatter->asTimestamp("now 00:00:00"),
                Yii::$app->formatter->asTimestamp("now 00:00:00 +1day")
            );

            if (!$data || !$data["byTariffs"] || !$data["byLines"]) {
                continue;
            }

            // === Общие итоги ===
            $totals = [
                "spent" => 0,
                "calls" => 0,
                "duration" => 0,
                "answered" => 0,
                "answered_10" => 0,
                "answered_10_duration" => 0,
            ];

            foreach ($data["byTariffs"] as $tariff) {
                $totals["spent"] += $tariff["total_spent"];
                $totals["calls"] += $tariff["total_in_calls_count"] + $tariff["total_out_calls_count"];
                $totals["duration"] += $tariff["total_in_calls_duration"] + $tariff["total_out_calls_duration"];
                $totals["answered"] += $tariff["total_in_calls_answered"] + $tariff["total_out_calls_answered"];
                $totals["answered_10"] += $tariff['total_in_calls_answered_10'] + $tariff['total_out_calls_answered_10'];
                $totals["answered_10_duration"] += $tariff['total_in_calls_answered_10_duration'] + $tariff['total_out_calls_answered_10_duration'];
            }

            // === Формируем текст ===
            $text = $this->buildSummaryText($user, $totals, $data["byLines"]);

            $user->sendMessageInTelegram($text);
        }
    }

    private function buildSummaryText(User $user, array $totals, array $lines): string
    {
        $formatter = Yii::$app->formatter;

        $total_duration_name = round($totals["duration"] / 60, 2) . " min";
        $total_answered_10_duration_name = round($totals["answered_10_duration"] / 60, 2) . " min";

        $text = "👤 <b>{$user->username}</b>" . PHP_EOL . PHP_EOL;
        $text .= "🗓️ " . $formatter->asDate('now') . PHP_EOL . PHP_EOL;
        $text .= "——— <i>Summary</i> ———" . PHP_EOL;
        $text .= "☎️ Total calls: {$totals["calls"]} ($total_duration_name)" . PHP_EOL;
        $text .= "🟢 Answered: {$totals["answered"]}" . PHP_EOL;
        $text .= "🟡 Answered >10sec: {$totals["answered_10"]} ($total_answered_10_duration_name)" . PHP_EOL;
        $text .= "💸 <b>Spent: " . $formatter->asCurrency($totals["spent"]) . "</b>" . PHP_EOL . PHP_EOL;

        $text .= "——— <i>Lines</i> ———" . PHP_EOL;

        foreach ($lines as $line) {
            $line_duration = round(($line["total_in_calls_duration"] + $line["total_out_calls_duration"]) / 60, 2) . " min";
            $line_answered_10_duration = round(($line["total_in_calls_answered_10_duration"] + $line["total_out_calls_answered_10_duration"]) / 60, 2) . " min";

            $text .= "┊┄ {$line["name"]}" . PHP_EOL;
            $text .= "┊☎️ Total calls: " . ($line["total_in_calls_count"] + $line["total_out_calls_count"]) . " ($line_duration)" . PHP_EOL;
            $text .= "┊🟢 Answered: " . ($line["total_in_calls_answered"] + $line["total_out_calls_answered"]) . PHP_EOL;
            $text .= "┊🟡 Answered >10sec: " . ($line["total_in_calls_answered_10"] + $line["total_out_calls_answered_10"]) . " ($line_answered_10_duration)" . PHP_EOL;
            $text .= "┊💸 <b>Spent: " . $formatter->asCurrency($line["total_spent"]) . "</b>" . PHP_EOL;
            $text .= "┊" . PHP_EOL;
        }

        return $text;
    }

}