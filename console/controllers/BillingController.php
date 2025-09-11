<?php

namespace console\controllers;

use common\enums\LineTariffEnum;
use common\models\Call;
use common\models\Line;
use common\models\User;
use Longman\TelegramBot\Request;
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

    public function actionDayNotifier($for_admin = false)
    {
        if($for_admin) {
            $users = User::find()
                ->all();
        } else {
            $users = User::find()
                ->where(["is not", "telegram_chat_id", null])
                ->all();
        }

        foreach ($users as $user) {
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

            if($for_admin) {
                Request::sendMessage([
                    "chat_id" => env('TELEGRAM_ADMIN_CHAT_ID'),
                    "text" => $text,
                    "parse_mode" => "HTML"
                ]);
            } else {
                $user->sendMessageInTelegram($text);
            }
        }
    }

    private function buildSummaryText(User $user, array $totals, array $lines): string
    {
        $formatter = Yii::$app->formatter;

        $total_duration_name = $this->secondsToMinutesAndSeconds($totals['duration']) . " min";
        $total_answered_10_duration_name = $this->secondsToMinutesAndSeconds($totals["answered_10_duration"]) . " min";
        $answered_percent = round($totals["answered"] * 100 / $totals["calls"], 2);
        $answered_10_percent = round($totals["answered_10"] * 100 / $totals["calls"], 2);

        $text = "👤 <b>{$user->username}</b>" . PHP_EOL . PHP_EOL;
        $text .= "🗓️ " . $formatter->asDate('now') . PHP_EOL . PHP_EOL;
        $text .= "——— <i>Summary</i> ———" . PHP_EOL;
        $text .= "☎️ Total calls: {$totals["calls"]} ($total_duration_name)" . PHP_EOL;
        $text .= "🟢 Answered: {$totals["answered"]} ($answered_percent%)" . PHP_EOL;
        $text .= "🟡 Answered >10sec: {$totals["answered_10"]} ($total_answered_10_duration_name, $answered_10_percent%)" . PHP_EOL;
        $text .= "💸 <b>Spent: " . $formatter->asCurrency($totals["spent"]) . "</b>" . PHP_EOL . PHP_EOL;

        $text .= "——— <i>Lines</i> ———" . PHP_EOL;

        foreach ($lines as $line) {
            $line_duration = $this->secondsToMinutesAndSeconds(($line["total_in_calls_duration"] + $line["total_out_calls_duration"])) . " min";
            $line_answered_10_duration = $this->secondsToMinutesAndSeconds(($line["total_in_calls_answered_10_duration"] + $line["total_out_calls_answered_10_duration"])) . " min";

            $total_calls = $line["total_in_calls_count"] + $line["total_out_calls_count"];
            $answered_count = $line["total_in_calls_answered"] + $line["total_out_calls_answered"];
            $answered_10_count = $line["total_in_calls_answered_10"] + $line["total_out_calls_answered_10"];
            $answered_percent = round($answered_count * 100 / $total_calls, 2);
            $answered_10_percent = round($answered_10_count * 100 / $total_calls, 2);

            $text .= "┊┄ {$line["name"]}" . PHP_EOL;
            $text .= "┊☎️ Total calls: " . $total_calls . " ($line_duration)" . PHP_EOL;
            $text .= "┊🟢 Answered: " . $answered_count . " ($answered_percent%)" . PHP_EOL;
            $text .= "┊🟡 Answered >10sec: " . $answered_10_count . " ($line_answered_10_duration, $answered_10_percent%)" . PHP_EOL;
            $text .= "┊💸 <b>Spent: " . $formatter->asCurrency($line["total_spent"]) . "</b>" . PHP_EOL;
            $text .= "┊" . PHP_EOL;
        }

        return $text;
    }


    private function secondsToMinutesAndSeconds(int $seconds): string
    {
        $minutes = intdiv($seconds, 60);     // минуты
        $remainingSeconds = $seconds % 60;   // остаток секунд

        return $minutes . ":" . $remainingSeconds;
    }

    public function actionHotFix()
    {
        $calls = Call::find()
            ->where(["line_id" => null, "direction" => "out"])
            ->all();

    }

}