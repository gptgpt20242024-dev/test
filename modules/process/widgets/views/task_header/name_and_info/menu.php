<?php

use app\modules\process\models\task\Req3Tasks;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */

?>

<div class="dropleft">
    <button class="btn btn-link" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-ellipsis-v"></i>
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">

        <?php $admin_btn = false; ?>
        <?php if ($task->isAccessLinkTask(Yii::$app->user->identity)): ?>
            <?php $admin_btn = true; ?>
            <a href="javascript:void (0);" class="dropdown-item" onclick="showDialogAction('Привязать к родителю', '/process/task/link-parent', {task_id: <?= $task->id ?>})">
                Родительская задача
            </a>
        <?php endif; ?>

        <?php if ($task->isAccessEditObservers(Yii::$app->user->identity)): ?>
            <?php $admin_btn = true; ?>
            <a href="javascript:void (0);" class="dropdown-item" onclick="showDialogAction('Изменение наблюдателей', '/process/task/set-observers', {task_id: <?= $task->id ?>})">
                Наблюдатели
            </a>
        <?php endif; ?>

        <?php if ($task->isAccessDelete(Yii::$app->user->identity)): ?>
            <?php $admin_btn = true; ?>
            <a href="javascript:void (0);" class="dropdown-item" onclick="showDialogDeleteTask(<?= $task->id ?>)">
                <span class="text-red">Удалить</span>
            </a>
        <?php endif; ?>

        <?php if ($task->isAccessEditName(Yii::$app->user->identity) || $task->isAccessEditFM(Yii::$app->user->identity)): ?>
            <?php $admin_btn = true; ?>
            <a href="javascript:void (0);" class="dropdown-item" onclick="showDialogAction('Изменение', '/process/task/edit', {id: <?= $task->id ?>})">
                Изменить
            </a>
        <?php endif; ?>


        <?php if ($admin_btn): ?>
            <div class="dropdown-divider"></div>
        <?php endif ?>


        <a href="javascript:void (0);" class="btn dropdown-item" onclick="showDialogTaskOpers(<?= $task->id ?>)">
            Участники <span style="font-size: x-small; color: #898989; float: right;">Alt + Q</span>
        </a>

        <a href="javascript:void (0);" class="dropdown-item" onclick="showDialogTaskStepHistory(<?= $task->id ?>)">
            Движение <span style="font-size: x-small; color: #898989; float: right;">Alt + W</span>
        </a>

        <?php if ($task->parent_task || count($task->sub_tasks) > 0): ?>
            <a href="<?= Url::toRoute(['/process/task/tree', 'task_id' => $task->id]) ?>" class="dropdown-item" style="color: #804717">
                Схема задач
            </a>
        <?php endif ?>

        <a href="javascript:void (0);" class="dropdown-item" onclick="showDialogStepInfo(<?= $task->step_id ?>)">
            Информация о шаге
        </a>

        <div class="dropdown-divider"></div>

        <a href="javascript:void (0);" class="dropdown-item" onclick="addStepRemark(<?= $task->id ?>, <?= $task->step_id ?>)">
            Улучшение
        </a>

        <?php if (count($task->chats) > 0): ?>
            <a href="javascript:void (0);" class="dropdown-item" onclick="showDialogInfo('История чатов', '/process/chat/history', {task_id: <?= $task->id ?>}, undefined, '[data-chats-history]', BootstrapDialog.TYPE_PRIMARY, BootstrapDialog.SIZE_LARGE)">
                История чатов
            </a>
        <?php endif ?>
    </div>
</div>