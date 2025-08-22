<?php

use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $value Req3TasksDataItems */
?>

<?php if ($value->reward_service): ?>
    <?= $value->reward_service->name ?>
<?php else: ?>
    reward_service<?= $value->value_id ?>
<?php endif; ?>