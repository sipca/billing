<?php
namespace common\enums;

use common\traits\EnumToArray;

enum CallStatusEnum: int
{
    use EnumToArray;
    case FAILED = 0;
    case ANSWERED = 1;
    case NO_ANSWER = 2;
    case BUSY = 3;
    case IN_PROGRESS = 10;

    public static function mapFromCdr(string $status) : self
    {
        return match ($status) {
            "ANSWERED" => self::ANSWERED,
            "BUSY" => self::BUSY,
            "NO ANSWER" => self::NO_ANSWER,
            default => self::FAILED,
        };
    }
}
