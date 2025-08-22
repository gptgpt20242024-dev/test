<?php

use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $value Req3TasksDataItems */
?>

<?php if ($value->phone): ?>
    <?= $value->phone->phone ?> <span style="color: #4e4e4e">(<?= $value->phone->fio ?>)</span>
<?php else: ?>
    phone<?= $value->value_id ?>
<?php endif; ?>

