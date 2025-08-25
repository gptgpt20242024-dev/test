<?php

use app\modules\process\models\task\Req3Tasks;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */

?>

<div>
    <?php $didTheRequiredJob = $task->isDeviationJobComplete(); ?>
    <?php if ($didTheRequiredJob !== null): ?>
        <?php if ($didTheRequiredJob): ?>
            <span class="badge badge-light" style="color: #317728"><i class="fas fa-check-double" style="color: #00c625"></i> Работа выполнена</span>
        <?php else: ?>
            <span class="badge badge-light" style="color: #a9a9a9"><i class="fas fa-times"></i> Работа не выполнена</span>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (!empty($task->queue_label_id) && $task->queue_label): ?>
        <span class="badge badge-light" style="background-color: <?= $task->queue_label->color ?>"><?= $task->queue_label->label ?></span>
    <?php endif; ?>

    <?php if (!empty($task->priority_value)): ?>
        <span style="color: #b05c30; font-size: small;" title="Приоритет" data-toggle="tooltip"><i class="fas fa-award"></i> <?= $task->priority_value > 1000 ? (round($task->priority_value / 1000, 0) . "k") : round($task->priority_value, 1) ?></span>
    <?php endif; ?>
</div>
<span style="color: #b6b6b6; font-size: small; cursor: pointer" data-link="<?= Url::toRoute(['/process/task/view', 'id' => $task->id], true) ?>" title="Скопировать ссылку на задачу" data-toggle="tooltip">#<?= $task->id ?></span>
