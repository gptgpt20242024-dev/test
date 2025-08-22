<?php

use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $value Req3TasksDataItems */
?>

<?php if ($value->list_tree_item): ?>
    <?= $value->list_tree_item->value ?>
<?php else: ?>
    list_tree_item<?= $value->value_id ?>
<?php endif; ?>
