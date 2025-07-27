<?php

namespace common\widgets;

use kartik\money\MaskMoney;

class MoneyControl extends MaskMoney
{
//    public     $maskedInputOptions = [
//        'suffix' => ' €',
//        'prefix' => '',
//        'allowMinus' => true,
//        'digits' => 2,
//        'radixPoint' => ',',
//        'groupSeparator' => '.',
////        'alias' => 'currency',
//        'numericInput' => 'true',
////        'mask' => '99.99'
//    ];
//
//    public $displayOptions = ["dir" => "rtl"];

    public $pluginOptions = [
        'prefix' => '$ ',
        'thousands' => '.',
        'decimal' => ',',
        'precision' => 2
    ];
}