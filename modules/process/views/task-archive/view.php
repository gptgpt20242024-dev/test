<?php

use app\modules\process\models\template_steps\Req3TemplateSteps;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $task app\modules\process\models\task_archive\TaskArchive */
/* @var $items array */
/* @var $timeExecute array|null */
/* @var $deviationInfo array */
/* @var $timeTemplate int|null */
/* @var $identifierIds int[] */
/* @var $identifiers app\modules\process\models\identifiers\Req3Identifiers[] */
/* @var $dataItems app\modules\process\models\task_data\Req3TasksDataItems[][] */
/* @var $relations array */


$this->title = "Архив: ".$task->task_name;
$this->params['breadcrumbs'][] = ['label' => "Архив", 'url' => ['index']];
$this->params['breadcrumbs'][] = $task->task_name;

$statuses = Req3TemplateSteps::LAST_STATUS_NAMES;
$statusLabel = $statuses[$task->step_last_status] ?? $task->step_last_status;
?>



<div class="task-archive-view">
    <div class="card mb-3">
        <div class="card-header">
            <h5 class="mb-0"><?= Html::encode($task->task_name) ?></h5>
            <div style="font-size: small"><?= Html::a($task->template_name, ['/process/templates/view', 'id' => $task->template_id], ['target' => '_blank']) ?></div>
        </div>
        <div class="card-body">
            <p class="mb-0"><strong>ID задачи:</strong> <?= $task->task_id ?></p>
            <p class="mb-0"><strong>Дата создания:</strong> <?= $task->task_date_create ?></p>
            <p class="mb-0"><strong>Дата перехода на последний шаг:</strong> <?= $task->task_date_start_step ?></p>
            <p class="mb-0"><strong>Шаг был последний:</strong> <?= $task->step_is_last ? 'Да' : 'Нет' ?></p>
            <p class="mb-0"><strong>Статус шага:</strong> <?= $statusLabel ?></p>
            <p class="mb-0"><strong>Дата добавления в архив:</strong> <?= $task->date_add_to_archive ?></p>
        </div>
    </div>

    <?php if (!empty($relations)): ?>
        <div class="card" data-relations-card="1">
            <div class="card-header" data-spoiler data-container="[data-relations-card]" data-content="[data-relations-content]">
                <i class="fas fa-caret-up" data-open="1"></i>
                <i class="fas fa-caret-down" data-close="1"></i>
                Связи
            </div>
            <div class="card-body" data-relations-content="1"  style="display: none; background: #dedede">
                <?= $this->render('relations', [
                    'relations' => $relations
                ]) ?>
            </div>
        </div>
    <?php endif; ?>


    <div class="card" data-history-card="1">
        <div class="card-header" data-spoiler data-container="[data-history-card]" data-content="[data-history-content]">
            <i class="fas fa-caret-up" data-open="1"></i>
            <i class="fas fa-caret-down" data-close="1"></i>
            История
        </div>
        <div class="card-body" data-history-content="1" style="display: none; background: #dedede">
            <?= $this->render('history', [
                'task' => $task,
                'items' => $items,
                'timeExecute' => $timeExecute,
                'deviationInfo' => $deviationInfo,
                'timeTemplate' => $timeTemplate,
            ]) ?>
        </div>
    </div>



    <?php if (!empty($identifiers)): ?>
        <div class="card" data-data-card="1">
            <div class="card-header" data-spoiler data-container="[data-data-card]" data-content="[data-data-content]">
                <i class="fas fa-caret-up" data-open="1"></i>
                <i class="fas fa-caret-down" data-close="1"></i>
                Данные
            </div>
            <div class="card-body" data-data-content="1"  style="display: none; background: #dedede">
                <?= $this->render('data', [
                    'identifierIds' => $identifierIds,
                    'identifiers' => $identifiers,
                    'dataItems' => $dataItems,
                ]) ?>
            </div>
        </div>
    <?php endif; ?>

</div>
