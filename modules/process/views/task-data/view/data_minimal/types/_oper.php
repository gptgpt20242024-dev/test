<?php

use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $value Req3TasksDataItems */
?>

<?php if ($value->oper): ?>
    <?= $value->oper->fio ?>
<?php else: ?>
    oper<?= $value->value_id ?>
<?php endif; ?>

