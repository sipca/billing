<?php

namespace common\enums;

use common\traits\EnumToArray;

enum LineTariffEnum : int
{
    use EnumToArray;

    case NULL = 0;
    case DAILY = 1;
    case WEEKLY = 2;
    case MONTHLY = 3;
    case HOURLY = 4;
}
