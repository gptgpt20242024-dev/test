<?php

use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $value Req3TasksDataItems */
?>

<?php if ($value->value_id == -1): ?>
    Авто
<?php elseif ($value->dp): ?>
    <?= $value->dp->generateName() ?>
<?php else: ?>
    dp<?= $value->value_id ?>
<?php endif; ?>

