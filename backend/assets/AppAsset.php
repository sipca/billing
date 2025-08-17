<?php

namespace backend\assets;

use rmrevin\yii\fontawesome\CdnProAssetBundle;
use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css?v=170825',
    ];
    public $js = [
        'https://cdn.jsdelivr.net/npm/chart.js',
        'js/charts.js?v=01'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
        CdnProAssetBundle::class
    ];
}
