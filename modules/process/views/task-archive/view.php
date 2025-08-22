<?php

use app\modules\process\models\template_steps\Req3TemplateSteps;
use app\modules\process\widgets\IdentifierViewWidget;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $task app\modules\process\models\task_archive\TaskArchive */
/* @var $items array */
/* @var $timeExecute array|null */
/* @var $deviationInfo array */
/* @var $timeTemplate int|null */
/* @var $identifiers app\modules\process\models\identifiers\Req3Identifiers[] */
/* @var $dataItems app\modules\process\models\task_data\Req3TasksDataItems[][] */

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

    <?php if (!empty($identifiers)): ?>
        <div class="card mb-3">
            <div class="card-header">Данные</div>
            <div class="card-body">
                <?php foreach ($identifiers as $identifierId => $identifier): ?>
                    <?= IdentifierViewWidget::widget([
                        'identification' => Yii::$app->user->identity,
                        'identifier' => $identifier,
                        'forced_data' => $dataItems[$identifierId] ?? [],
                        'is_only_view' => true,
                    ]) ?>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <div data-history-card="1" class="card">
        <div class="card-header" data-spoiler data-container="[data-history-card]" data-content="[data-history-content]">
            История
            <i class="fas fa-caret-up" data-open="1"></i>
            <i class="fas fa-caret-down" data-close="1"></i>
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
</div>
