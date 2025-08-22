<?php

use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $value Req3TasksDataItems */
?>

<?php if ($value->doc_type): ?>
    <?= $value->doc_type->name ?>
<?php else: ?>
	doc_type_<?= $value->value_id ?>
<?php endif; ?>

