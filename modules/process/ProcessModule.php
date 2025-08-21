<?php

namespace app\modules\process;

use app\modules\process\assets\ProcessAsset;
use Yii;
use yii\base\Module;
use yii\web\Application as WebApplication;

class ProcessModule extends Module
{
    public function init()
    {
        parent::init();
        Yii::configure($this, require(__DIR__ . '/config/web.php'));

        if (Yii::$app instanceof WebApplication) {
            ProcessAsset::register(Yii::$app->view);
        }
    }
}
