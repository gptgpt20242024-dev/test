<?php

use app\modules\process\models\task\Req3Tasks;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
?>

<div style="display: flex; align-items: flex-start; gap: 10px;">

    <div style="width: 100%; min-width:0">

        <div style="float: right; text-align: right">
            <?= $this->render('name_and_info/right', ['task' => $task]) ?>
        </div>

        <?= $this->render('name_and_info/main', ['task' => $task]) ?>

    </div>

    <button type="button" class="btn btn-light" title="Изменения в шаблоне" style="position: relative" onclick="showDialogTaskChangeLog(<?= $task->id ?>)">
        <i class="fas fa-bell" style="color: #989898"></i>
        <?php if ($task->hasNewChangeLog(Yii::$app->user->id, !Yii::$app->user->canMulti(["business.admin", "business.delete", "business.edit"]))): ?>
            <i class="fas fa-circle" style="position: absolute; top: 2px; right: 2px; color: rgb(255, 97, 99); -webkit-text-stroke: 1px #FFFFFF; text-stroke: 1px #FFFFFF;" data-new-change="1" title="Есть новые изменения"></i>
        <?php endif; ?>
    </button>

    <?= $this->render('name_and_info/menu', ['task' => $task]) ?>

</div>