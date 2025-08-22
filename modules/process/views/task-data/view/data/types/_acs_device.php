<?php

use app\modules\acs\enrichers\AcsHouseEnricher;
use app\modules\acs\services\AcsService;
use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $identifier Req3Identifiers */
/* @var $value Req3TasksDataItems */

/* @var $is_editable boolean */
/* @var $is_required boolean */
/* @var $is_only_view boolean */

/* @var $can_edit boolean */

$acsService = Yii::$container->get(AcsService::class);
$acsEnricher = Yii::$container->get(AcsHouseEnricher::class);
$device = $acsService->getDeviceById($value->value_id);
$acsEnricher->enrich($device);
?>

<?= $device ? $device->getName() : "#{$value->value_id}" ?>