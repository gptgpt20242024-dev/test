<?php

use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $value Req3TasksDataItems */
?>

<?php if ($value->block_reason): ?>
    <?= $value->block_reason->name ?>
<?php else: ?>
    block_reason<?= $value->value_id ?>
<?php endif; ?>

