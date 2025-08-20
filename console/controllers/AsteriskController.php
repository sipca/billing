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

        $events = $response->getEvents();

        /**
         * [keys:protected] => Array
         * (
         * [event] => ContactList
         * [actionid] => 175.3557
         * [objecttype] => contact
         * [objectname] =>
         * [viaaddr] =>
         * [qualifytimeout] => 3.000000
         * [qualify2xxonly] => false
         * [callid] =>
         * [regserver] =>
         * [pruneonboot] => no
         * [path] =>
         * [endpoint] =>
         * [viaport] =>
         * [authenticatequalify] => no
         * [uri] => sip:9002@1.1.1.1:50872;x-ast-orig-host=2.2.2.2:5060
         * [qualifyfrequency] => 60
         * [useragent] => dble
         * [expirationtime] => 17556515
         * [outboundproxy] =>
         * [status] => Reachable
         * [roundtripusec] => 54546
         * )
         */
        foreach ($events as $event) {
            echo $event->getKey('uri') . PHP_EOL;
        }

//        print_r($response);
        echo "\n";
    }
}