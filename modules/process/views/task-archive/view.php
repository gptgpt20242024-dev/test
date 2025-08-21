<?php

use yii\helpers\Html;
use app\modules\process\models\template_steps\Req3TemplateSteps;

/* @var $this yii\web\View */
/* @var $task app\modules\process\models\task_archive\TaskArchive */
/* @var $items array */

$this->title = $task->task_name;

$statusLabel = $task->step_last_status;
if (class_exists(Req3TemplateSteps::class) && method_exists(Req3TemplateSteps::class, 'statusList')) {
    $statuses = Req3TemplateSteps::statusList();
    if (is_array($statuses)) {
        $statusLabel = $statuses[$task->step_last_status] ?? $statusLabel;
    }
}
?>

<div class="task-archive-view">
    <div class="card mb-3">
        <div class="card-header">
            <?= Html::encode($task->task_name) ?>
        </div>
        <div class="card-body">
            <p class="mb-0"><strong>ID задачи:</strong> <?= Html::encode($task->task_id) ?></p>
            <p class="mb-0"><strong>Шаблон:</strong> <?= Html::encode($task->template_name) ?> (ID: <?= Html::encode($task->template_id) ?>)</p>
            <p class="mb-0"><strong>Дата создания:</strong> <?= Html::encode($task->task_date_create) ?></p>
            <p class="mb-0"><strong>Дата начала шага:</strong> <?= Html::encode($task->task_date_start_step) ?></p>
            <p class="mb-0"><strong>Дата добавления в архив:</strong> <?= Html::encode($task->date_add_to_archive) ?></p>
            <p class="mb-0"><strong>Последний шаг:</strong> <?= $task->step_is_last ? 'Да' : 'Нет' ?></p>
            <p class="mb-0"><strong>Статус шага:</strong> <?= Html::encode($statusLabel) ?></p>
            <p class="mb-0"><strong>Шаблон:</strong> <?= Html::encode($task->template_name) ?></p>
        </div>
    </div>

    <div data-history-card="1" class="card">
        <div class="card-header" data-spoiler data-container="[data-history-card]" data-content="[data-history-content]">
            История
            <i class="fas fa-caret-up" data-open="1"></i>
            <i class="fas fa-caret-down" data-close="1"></i>
        </div>
        <div class="card-body" data-history-content="1" style="display: none">
            <?= $this->render('history', ['task' => $task, 'items' => $items]) ?>
        </div>
    </div>
</div>
