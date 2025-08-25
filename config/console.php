<?php
use yii\helpers\ArrayHelper;

return ArrayHelper::merge(
    require __DIR__ . '/di.php',
    [
        // other console application configuration can be added here
    ]
);
