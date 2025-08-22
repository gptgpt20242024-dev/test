<?php

use app\modules\ktv\services\KtvService;
use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $value Req3TasksDataItems */

$ktvService = Yii::$container->get(KtvService::class);
$device = $ktvService->getDeviceById($value->value_id);
?>

<?= $device->name ?? "#{$value->value_id}" ?>