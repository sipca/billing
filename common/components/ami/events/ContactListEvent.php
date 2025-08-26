<?php

namespace common\components\ami\events;

use PAMI\Message\Event\EventMessage;

class ContactListEvent extends EventMessage
{
    public function getEndpoint() : string
    {
        return $this->getKey('endpoint');
    }

    public function getViaaddr() :string
    {
        return $this->getKey('viaaddr');
    }

    public function getUri() : string
    {
        return $this->getKey('uri');
    }

    public function getUseragent() :string
    {
        return $this->getKey('useragent');
    }

    public function getStatus() : string
    {
        return $this->getKey('status');
    }

    public function isReachable() : bool
    {
        return $this->getStatus() === 'Reachable';
    }
}