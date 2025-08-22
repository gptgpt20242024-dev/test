<?php

use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $identifier Req3Identifiers */
/* @var $value Req3TasksDataItems */

/* @var $is_editable boolean */
/* @var $is_required boolean */
/* @var $is_only_view boolean */

/* @var $can_edit boolean */

?>

<?php if ($value->call_name): ?>
    <?= $value->call_name->name ?>
    <span style="color: #4e86ff">(<?= $value->call_name->called_id ?>)</span>
<?php else: ?>
    call_name<?= $value->value_id ?>
<?php endif; ?>