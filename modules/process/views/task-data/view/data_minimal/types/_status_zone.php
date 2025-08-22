<?php

use app\modules\process\models\task_data\Req3TasksDataItems;
use app\modules\setting\constants\SettingStatusZone;

/* @var $this yii\web\View */
/* @var $value Req3TasksDataItems */

?>

<?= SettingStatusZone::getName($value->value_id) ?>

