<?php

namespace common\enums;

use common\traits\EnumToArray;

enum CallTariffTypeEnum : int
{
    use EnumToArray;
    case MIN_SEC = 1;
    case MIN_MIN = 2;
    case SEC = 3;

}
