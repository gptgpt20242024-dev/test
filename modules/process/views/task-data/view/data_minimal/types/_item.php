<?php

use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $value Req3TasksDataItems */
?>

<?php if ($value->item): ?>
    <?= $value->item->name ?>
<?php else: ?>
    item<?= $value->value_id ?>
<?php endif; ?>
<span style="color: #08b100; font-weight: bold">(<?= $value->value_number ?>)</span>

