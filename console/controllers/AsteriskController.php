<?php

namespace console\controllers;

use common\components\PjsipShowContactsAction;
use PAMI\Client\Impl\ClientImpl;
use PAMI\Message\Action\CommandAction;
use PAMI\Message\Action\CoreShowChannelsAction;
use PAMI\Message\Action\SIPPeersAction;
use yii\console\Controller;

class AsteriskController extends Controller
{
    public function actionIndex()
    {
        $options = [
            'host' => env("AMI_HOST"),
            'port' => 5038,
            'username' => env("AMI_USERNAME"),
            'secret' => env("AMI_SECRET"),
            'connect_timeout' => 100,
            'read_timeout' => 1000
        ];

        $client = new ClientImpl($options);
        $client->open();

        $response = $client->send(new PjsipShowContactsAction());

        print_r($response);
        echo "\n";
    }
}