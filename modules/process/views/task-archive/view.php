<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $task app\modules\process\models\task_archive\TaskArchive */
/* @var $items array */

$this->title = $task->task_name;
?>

<div class="task-archive-view">
    <div class="card mb-3">
        <div class="card-header">
            <?= Html::encode($task->task_name) ?>
        </div>
        <div class="card-body">
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
