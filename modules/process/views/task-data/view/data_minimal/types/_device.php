<?php

use app\modules\process\models\task_data\Req3TasksDataItems;
use app\modules\userside\services\DeviceService;

/* @var $this yii\web\View */
/* @var $value Req3TasksDataItems */

$deviceService = Yii::$container->get(DeviceService::class);
$switch = $deviceService->getById($value->value_id);
?>

<?php if ($switch): ?>
    <?= $switch->name ?>
    <?php if (!empty($switch->ip)): ?>
        (<?= $switch->ip ?>)
    <?php endif; ?>
    - <?= $switch->location ?>
<?php else: ?>
    <?= $value->value_id ?>
<?php endif; ?>