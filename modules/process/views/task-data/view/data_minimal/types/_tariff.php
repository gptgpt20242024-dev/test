<?php

use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $value Req3TasksDataItems */
?>

<?php if ($value->tariff): ?>
    <?= $value->tariff->name ?>
<?php else: ?>
    tariff<?= $value->value_id ?>
<?php endif; ?>

