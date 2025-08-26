<?php

namespace common\components\ami\actions;

use PAMI\Message\Action\ActionMessage;

class PjsipShowContactsAction extends ActionMessage
{
    public function __construct()
    {
        parent::__construct("PJSIPShowContacts");
    }
}