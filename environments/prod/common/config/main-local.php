<?php

return [
    'components' => [
        'db' => [
            'class' => \yii\db\Connection::class,
            'dsn' => 'mysql:host='.env('MYSQL_HOST').';dbname='.env('MYSQL_DB'),
            'username' => env('MYSQL_USER'),
            'password' => env('MYSQL_PASSWORD'),
            'charset' => 'utf8',
        ],
        'asteriskdb' => [
            'class' => \yii\db\Connection::class,
            'dsn' => 'mysql:host='.env('AST_MYSQL_HOST').';dbname='.env('AST_MYSQL_DB'),
            'username' => env('AST_MYSQL_USER'),
            'password' => env('AST_MYSQL_PASSWORD'),
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@common/mail',
        ],
    ],
];
