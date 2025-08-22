<?php

use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $value Req3TasksDataItems */
?>

<?php if ($value->vfp): ?>
    <?= $value->vfp->short_product ?>
<?php else: ?>
    vfp<?= $value->value_id ?>
<?php endif; ?>

