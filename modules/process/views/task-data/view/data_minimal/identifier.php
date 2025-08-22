<?php

use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\Req3Scheduler;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $task Req3Tasks|Req3Scheduler */
/* @var $identifier Req3Identifiers */
/* @var $show_empty boolean */

if (!isset($show_empty)) {
    $show_empty = true;
}

$is_multi = $identifier->is_multi;

$values = [];
foreach ($task->data as $value) {
    if ($value->identifier_id == $identifier->id && $value->type == $identifier->type) {
        $values[] = $value;
        if (!$is_multi) break;
    }
}

?>

<?php if ($show_empty || count($values) > 0): ?>

    <?= $this->render('@app/modules/process/views/identifiers/_icon_type', ['type' => $identifier->type]) ?>
    <b> <?= $identifier->name ?>:</b>


    <?php if ($identifier->type == Req3Identifiers::TYPE_CALL_STATUS && $task instanceof Req3Tasks && count($task->calls_statuses_step) > 0): ?>
        <?php $values = [new Req3TasksDataItems(['type' => Req3Identifiers::TYPE_CALL_STATUS, 'identifier_id' => $identifier->id])]; ?>
    <?php endif; ?>

    <?php if (count($values) > 0): ?>
        <?php $i = 0; ?>
        <?php foreach ($values as $value): ?>
            <?php if ($i++ > 0 && $identifier->type != Req3Identifiers::TYPE_GROUP) echo ", " ?>
            <?= $this->render('type', ['task' => $task, 'identifier' => $identifier, 'value' => $value]) ?>
        <?php endforeach; ?>
    <?php else: ?>
        <span style="color: #808080">Данные не заполнены</span>
    <?php endif; ?>

<?php endif; ?>
