<?php

use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task\Req3TaskStartedSubTask;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $taskLink Req3TaskStartedSubTask */

?>

<div class="card m-0" style="overflow: hidden; width: 400px; max-width: 100%">
    <?php if ($taskLink->sub_task): ?>
        <div class="card-body px-3 py-2" style="display: flex; align-items: center;">
            <div style="flex-grow: 1; overflow: hidden;">
                <a href="<?= Url::toRoute(['/process/task/view', 'id' => $taskLink->sub_task_id]) ?>" target="_blank">
                    <div style="white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
                        <?= $taskLink->sub_task->name ?>
                    </div>
                </a>
            </div>
            <?php if ($task->isAccessLinkTask(Yii::$app->user->identity)): ?>
                <div>
                    <i class="fas fa-times fa-1x" style="color: #919191;" onclick="showDialogUnlinkTask(<?= $taskLink->id ?>)"></i>
                </div>
            <?php endif; ?>
        </div>
        <?= $this->render('/task/view/progress/all', ['task' => $taskLink->sub_task]) ?>
    <?php else: ?>
        <div class="card-body px-3 py-2" style="text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
            <?php $taskLink_deleted = Req3Tasks::find(false)->id($taskLink->sub_task_id)->one(); ?>
            <span style="color: #b71100; text-decoration: line-through"><?= $taskLink_deleted->name ?? "-" ?></span>
            <div style="color: #777777; font-size: small">Задача (#<?= $taskLink->sub_task_id ?>) удалена</div>
        </div>
    <?php endif; ?>
</div>
