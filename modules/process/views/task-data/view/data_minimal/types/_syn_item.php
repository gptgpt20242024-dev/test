<?php

use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $value Req3TasksDataItems */
?>
<?php if ($value->syn_item): ?>
    <?= $value->syn_item->name ?>
<?php else: ?>
    item<?= $value->value_id ?>
<?php endif; ?>