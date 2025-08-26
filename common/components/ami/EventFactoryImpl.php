<?php

namespace common\components\ami;

use PAMI\Message\Event\EventMessage;
use PAMI\Message\Event\UnknownEvent;
use PAMI\Message\Message;

class EventFactoryImpl extends \PAMI\Message\Event\Factory\Impl\EventFactoryImpl
{
    public static function createFromRaw(string $message): EventMessage
    {
        $eventStart = strpos($message, 'Event: ') + 7;
        $eventEnd = strpos($message, Message::EOL, $eventStart);
        if ($eventEnd === false) {
            $eventEnd = strlen($message);
        }
        $name = substr($message, $eventStart, $eventEnd - $eventStart);
        $parts = explode('_', $name);
        $totalParts = count($parts);
        for ($i = 0; $i < $totalParts; $i++) {
            $parts[$i] = ucfirst($parts[$i]);
        }
        $name = implode('', $parts);
        $className = '\\PAMI\\Message\\Event\\' . $name . 'Event';
        $className2 = '\\common\\components\\ami\\events\\' . $name . 'Event';

        if (class_exists($className, true)) {
            return new $className($message);
        }

        if (class_exists($className2, true)) {
            return new $className2($message);
        }
        return new UnknownEvent($message);
    }
}