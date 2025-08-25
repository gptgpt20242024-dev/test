<?php

use app\modules\process\models\task\Req3Tasks;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */

?>

<?php if ($task->parent_task): ?>
    <div class="btn-group mb-2 mr-1" style="max-width: 100%">
        <a href="<?= Url::toRoute(['/process/task/view', 'id' => $task->parent_task->task_id]) ?>" target="_blank" class="btn btn-outline-light btn-sm p-0 shadow-sm" title="Родительская задача" data-toggle="tooltip" style="text-align: left; user-select: auto; color: #004f95; min-width: 200px; border-color: #cbcbcb;">
            <div class="px-2 py-1" style="display: flex; align-items: center; font-weight: bold; ">
                <i class="fas fa-arrow-left fa-1x mr-2" style="color: #919191; cursor: pointer"></i>
                <div>

                    <?php if ($task->parent_task->task): ?>
                        <div style="font-size: 11px; color: #515151; font-weight: normal">
                            <?= $task->parent_task->task->template->name ?? "-" ?>
                        </div>
                        <?= $task->parent_task->task->name ?>
                    <?php else: ?>

                        <?php $sub_task_deleted = Req3Tasks::find(false)->id($task->parent_task->task_id)->one(); ?>
                        <div style="font-size: 11px; color: #515151; font-weight: normal">
                            <?= $sub_task_deleted->template->name ?? "-" ?>
                        </div>

                        <span style="color: #b71100; text-decoration: line-through"><?= $sub_task_deleted->name ?? "-" ?></span>
                    <?php endif; ?>

                </div>
            </div>

            <?php if ($task->parent_task->task): ?>
                <?= $this->render('/task/view/progress/step', ['task' => $task->parent_task->task]) ?>
            <?php else: ?>
                <div class="progress" style="height: 19px;">
                    <div class="progress-bar bg-danger" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                        <div>
                            <i class="fas fa-skull-crossbones" style="color: #811111"></i>
                            Задача удалена
                        </div>
                    </div>
                </div>
            <?php endif; ?>


        </a>
        <?php if ($task->isAccessLinkTask(Yii::$app->user->identity)): ?>
            <button type="button" onclick="showDialogUnlinkTask(<?= $task->parent_task->id ?>)" class="btn btn-outline-light btn-sm" title="Отвязать родителя" data-toggle="tooltip" style="display: flex; align-items: center; border-color: #cbcbcb;">
                <i class="fas fa-times fa-1x" style="color: #919191;"></i>
            </button>
        <?php endif; ?>
    </div>

<?php endif; ?>