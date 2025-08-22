<?php

use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $value Req3TasksDataItems */
?>

<?php if ($value->template): ?>
    <?= $value->template->name ?>
<?php else: ?>
    template<?= $value->value_id ?>
<?php endif; ?>

