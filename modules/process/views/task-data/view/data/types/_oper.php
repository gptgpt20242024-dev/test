<?php

use app\models\Opers;
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

<?php if ($value->oper): ?>
    <?= $value->oper->fio ?>
<?php else: ?>
    <span style="color: #98140c; text-decoration: line-through"><?= Opers::getFio($value->value_id) ?></span>
<?php endif; ?>

