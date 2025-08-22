<?php

use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $value Req3TasksDataItems */
?>

<?php if ($value->service): ?>
    <?= $value->service->service_name ?>
<?php else: ?>
    service<?= $value->value_id ?>
<?php endif; ?>

