<?php

namespace console\controllers;

use common\models\User;
use Yii;
use yii\console\Controller;

class UserController extends Controller
{
    public function actionCreateAdmin($username, $password)
    {
        $user = new User([
            "username" => $username,
            "email" => $username . "@example.com",
            "password_hash" => \Yii::$app->security->generatePasswordHash($password),
            "role" => User::ROLE_ADMIN,
            "auth_key" => Yii::$app->security->generateRandomString(),
            "access_token" => Yii::$app->security->generateRandomString(),
        ]);

        if($user->save()) {
            echo "User $username created\n";
        } else {
            print_r($user->getErrors());
        }
    }
}