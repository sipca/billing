<?php

namespace common\components;

use PAMI\Message\Action\ActionMessage;

class PjsipShowContactsAction extends ActionMessage
{
    public function __construct()
    {
        parent::__construct("PJSIPShowContacts");
    }
}