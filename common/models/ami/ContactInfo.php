<?php

namespace common\models\ami;

use yii\base\Model;

class ContactInfo extends Model
{
    public $uri;
    public $endpoint;
    public $status;
    public $viaaddr;
    public $isReachable;
}