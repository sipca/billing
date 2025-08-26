<?php

namespace common\bootstrap;

use common\enums\TransactionStatusEnum;
use common\enums\TransactionTypeEnum;
use common\models\Call;
use common\models\Transaction;
use Longman\TelegramBot\Telegram;
use PAMI\Client\Impl\ClientImpl;
use PAMI\Message\Event\Factory\Impl\EventFactoryImpl;
use Yii;
use yii\base\BootstrapInterface;
use yii\base\Event;

class AppBootstrap implements BootstrapInterface
{

    public function bootstrap($app)
    {
        $telegram = new Telegram(env('TELEGRAM_BOT_API_KEY'));

        Event::on(Call::class, Call::EVENT_AFTER_INSERT, function (Event $event) {
           /** @var Call $call */
           $call = $event->sender;
           $string = "Automatic charge";

           if(!$call?->line?->users) return;

           foreach ($call->line->users as $user) {
               $user = $user->id;
               break;
           }
           if(isset($user)) {
               $sum = round($call->getSum());
               if($sum > 0) {
                   Transaction::create($user, TransactionTypeEnum::AUTOMATIC, -$sum, $string . " " . $call->call_id, TransactionStatusEnum::PAID);
               }
           }
        });

        $ami_config = [
            'host' => env("AMI_HOST"),
            'port' => 5038,
            'username' => env("AMI_USERNAME"),
            'secret' => env("AMI_SECRET"),
            'connect_timeout' => 100,
            'read_timeout' => 1000,
            'eventFactory' => new \common\components\ami\EventFactoryImpl()
        ];
        $client = new \common\components\ami\ClientImpl($ami_config);
        Yii::$app->set("ami", $client);
    }
}