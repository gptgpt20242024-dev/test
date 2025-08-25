<?php

use app\modules\process\models\task\Req3Tasks;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */

?>

<div class="btn-group mb-2 mr-1" style="max-width: 100%">
    <a href="<?= Url::toRoute(['/process/task/view', 'id' => $task->id]) ?>" target="_blank" class="btn btn-outline-light btn-sm p-0 shadow-sm" title="Родительская задача" data-toggle="tooltip" style="text-align: left; user-select: auto; color: #004f95; min-width: 200px; border-color: #cbcbcb;">
        <div class="px-2 py-1" style="display: flex; align-items: center; font-weight: bold; ">
            <i class="fas fa-arrow-left fa-1x mr-2" style="color: #919191; cursor: pointer"></i>
            <div>
                <div style="font-size: 11px; color: #515151; font-weight: normal">
                    <?= $task->template->name ?? "-" ?>
                </div>
                <?= $task->name ?>
            </div>
        </div>

        <?= $this->render('/task/view/progress/step', ['task' => $task]) ?>

    </a>
</div>