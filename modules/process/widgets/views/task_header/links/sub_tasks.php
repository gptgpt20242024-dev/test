<?php

use app\modules\process\models\task\Req3Tasks;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $subTasks array */
?>

<?php $setActive = false; ?>
    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
        <?php foreach ($subTasks as $type => $data): ?>
            <button type="button" class="btn btn-sm btn-outline-secondary" data-spoiler data-container=".card" data-content="[data-spoiler-content-<?= $type ?>]" data-group="sub_tasks" style="cursor: pointer">
                <i class="far fa-folder-open mr-1" style="<?= (!$setActive) ? '' : 'display: none;' ?>" data-open="1"></i>
                <?= $data['name'] ?> (<?= count($data['tasks']) ?>)
            </button>
            <?php $setActive = true; ?>
        <?php endforeach; ?>
        <a href="<?= Url::toRoute(['/process/task/tree', 'task_id' => $task->id]) ?>" class="btn btn-sm btn-outline-info" style="margin-left: auto">
            Схема задач
        </a>
    </div>

<?php $setActive = false; ?>
<?php foreach ($subTasks as $type => $data): ?>
    <div data-spoiler-content-<?= $type ?>="1" style="<?= (!$setActive) ? '' : 'display: none;' ?> margin-top: 10px;">
        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
            <?php foreach ($data['tasks'] as $taskLink): ?>
                <?= $this->render('sub_task', ['task' => $task, 'taskLink' => $taskLink]); ?>
            <?php endforeach; ?>
            <?php $setActive = true; ?>
        </div>
    </div>
<?php endforeach; ?>