<?php

use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $value Req3TasksDataItems */
?>

<?php if ($value->wh_template): ?>
    <?= $value->wh_template->name ?>
<?php else: ?>
    wh_template<?= $value->value_id ?>
<?php endif; ?>

