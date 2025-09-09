<?php

namespace frontend\models;

use common\models\Line;
use PAMI\Client\Impl\ClientImpl;
use PAMI\Message\Action\OriginateAction;
use yii\base\Model;

class DialerForm extends Model
{
    public $numbers;
    public $lines;

    public function rules()
    {
        return [
            [['numbers', 'lines'], 'required'],
            [['numbers', 'lines'], 'safe'],
        ];
    }

    public function dial()
    {
        $dialer_trunk = env('DIALER_TRUNK');
        $dialer_context = env('DIALER_CONTEXT');
        $driver = env("AST_DRIVER", "PJSIP");

        $options = [
            'host' => env("AMI_HOST"),
            'port' => 5038,
            'username' => env("AMI_USERNAME"),
            'secret' => env("AMI_SECRET"),
            'connect_timeout' => 10,
            'read_timeout' => 100
        ];

        $client = new ClientImpl($options);
        $client->open();

        $numbers = array_filter(array_map('trim', explode("\n", $this->numbers)));

        $extString = "";
        $lines = Line::find()
            ->where(["id" => $this->lines])
            ->all();

        foreach ($lines as $line) {
            if($extString) {
                $extString .= "&";
            }
            $extString .= $driver."/" .$line->sip_num;
        }
//        print_r($extString.PHP_EOL);

        foreach ($numbers as $line) {
            $explode = explode(',', $line);
            if(count($explode) >= 2) {
                [$phone, $name] = array_map('trim', explode(',', $line));
            } else {
                $phone = $name = trim($line);
            }

            $channel = "$driver/$phone@$dialer_trunk";

//            print_r($channel . PHP_EOL);

            $originate = new OriginateAction($channel);
            $originate->setContext($dialer_context);
            $originate->setExtension(100);
            $originate->setCallerId($dialer_trunk);
            $originate->setPriority(1);
            $originate->setAsync(true);
            $originate->setTimeout(20000);
            $originate->setVariable('CLIENT_NAME', $name);
            $originate->setVariable('CALLER_ID_NUMBER', $phone);
            $originate->setVariable('CALLER_ID_CDR', "100 <100>");
            $originate->setVariable('INTERNAL_EXT', 100);

            \Yii::debug($originate->serialize());

            $client->send($originate);
            usleep(500000);
        }

    }
}