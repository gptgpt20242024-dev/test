<?php

use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $value Req3TasksDataItems */
?>

<?php if ($value->call_name): ?>
    <?= $value->call_name->name ?>
<?php else: ?>
    call_name<?= $value->value_id ?>
<?php endif; ?>

