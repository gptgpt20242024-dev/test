<?php

use app\modules\acs\enrichers\AcsHouseEnricher;
use app\modules\acs\services\AcsService;
use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $value Req3TasksDataItems */

$acsService = Yii::$container->get(AcsService::class);
$acsEnricher = Yii::$container->get(AcsHouseEnricher::class);
$device = $acsService->getDeviceById($value->value_id);
$acsEnricher->enrich($device);
?>

<?= $device ? $device->getName() : "#{$value->value_id}" ?>
