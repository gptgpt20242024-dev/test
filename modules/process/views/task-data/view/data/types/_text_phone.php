<?php

use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItems;
use app\widgets\oktelldial\OktellDial;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $identifier Req3Identifiers */
/* @var $value Req3TasksDataItems */

/* @var $is_editable boolean */
/* @var $is_required boolean */
/* @var $is_only_view boolean */

/* @var $can_edit boolean */

?>

<?php if ($value->phone): ?>
    <?= $value->phone->phone ?> <span style="color: #4e4e4e">(<?= $value->phone->fio ?>)</span>

    <?= OktellDial::widget([
        'phone'     => $value->phone->phone,
        'linkClass' => "btn btn-xs btn-link",
        'text'      => "(<i class=\"fas fa-phone\"></i> Позвонить)",
		'fm_id' => $task->fm_id
    ]); ?>
<?php else: ?>
    phone<?= $value->value_id ?>
<?php endif; ?>

