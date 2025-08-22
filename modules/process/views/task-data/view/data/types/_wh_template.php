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

<?php if ($value->wh_template): ?>
    <?= $value->wh_template->name ?>
<?php else: ?>
    wh_template<?= $value->value_id ?>
<?php endif; ?>

