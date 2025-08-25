<?php
use yii\helpers\ArrayHelper;

return ArrayHelper::merge(
    require __DIR__ . '/di.php',
    [
        // other web application configuration can be added here
    ]
);
