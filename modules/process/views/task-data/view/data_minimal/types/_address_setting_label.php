<?php

use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $value Req3TasksDataItems */
?>

<?php if ($value->label): ?>
    <?= $value->label->label ?>
<?php else: ?>
    label<?= $value->value_id ?>
<?php endif; ?>

