<?php

namespace common\models\ami;

use common\models\Line;
use PAMI\Message\Action\CoreShowChannelsAction;
use PAMI\Message\Event\CoreShowChannelEvent;
use yii\base\Model;
use yii\data\ArrayDataProvider;

class LiveCalls extends Model
{
    public function search()
    {
        $client = \Yii::$app->ami;
        $client->open();

        $response = $client->send(new CoreShowChannelsAction());
        $events = $response->getEvents();
        $models = $_models = [];

        foreach ($events as $event) {
            if (!$event instanceof CoreShowChannelEvent) continue;
            $_models[] = $event->getKeys();
        }

        $models = $this->normalizeCalls($_models);

        foreach ($models as &$model) {
            $line = Line::find()->where(["or", ["sip_num" => $model["from"]], ["sip_num" => $model["to"]]])->one();
            $model["line"] = $line?->name;
        }

//        foreach ($_models as $model) {
//            $model[]
//        }

        $dataProvider = new ArrayDataProvider([
            "allModels" => $models
        ]);
        return $dataProvider;
    }

    private function normalizeCalls(array $channels): array
    {
        $calls = [];

        foreach ($channels as $ch) {
            // Группируем по bridgeid (если звонок уже в разговоре) или linkedid (для setup'а)
            $key = $ch['bridgeid'] ?: $ch['linkedid'];

            if (!isset($calls[$key])) {
                $calls[$key] = [
                    'from'      => null,
                    'from_name' => null,
                    'to'        => null,
                    'to_name'   => null,
                    'duration'  => $ch['duration'],
                    'status'    => $ch['channelstatedesc'],
                ];
            }

            // Определяем "кто звонит"
            if (!empty($ch['calleridnum'])) {
                if ($calls[$key]['from'] === null) {
                    $calls[$key]['from'] = $ch['calleridnum'];
                    $calls[$key]['from_name'] = $ch['calleridname'] ?? '';
                }
            }

            // Определяем "кому звонят"
            if (!empty($ch['connectedlinenum'])) {
                if ($calls[$key]['to'] === null) {
                    $calls[$key]['to'] = $ch['connectedlinenum'];
                    $calls[$key]['to_name'] = $ch['connectedlinename'] ?? '';
                }
            }

            // Обновляем длительность (берём максимальную по группе)
            if (strcmp($ch['duration'], $calls[$key]['duration']) > 0) {
                $calls[$key]['duration'] = $ch['duration'];
            }

            // Если хотя бы один канал в "Up" → считаем звонок активным
            if ($ch['channelstatedesc'] === 'Up') {
                $calls[$key]['status'] = 'Up';
            }
        }

        return array_values($calls);
    }

}