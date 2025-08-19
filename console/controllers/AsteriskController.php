<?php

namespace console\controllers;

use PAMI\Client\Impl\ClientImpl;
use PAMI\Message\Action\CommandAction;
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
            'connect_timeout' => 10,
            'read_timeout' => 100
        ];

        $client = new ClientImpl($options);
        $client->open();

        $response = $client->send(new CommandAction("pjsip show contacts"));

        print_r($response->getMessage());
    }
}