<?php

use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItemProjectTree;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $identifier Req3Identifiers */
/* @var $node Req3TasksDataItemProjectTree */
/* @var $target_task Req3Tasks */
/* @var $key array */

/* @var $is_editable boolean */
/* @var $is_only_view boolean */
/* @var $can_edit boolean */

/* @var $subTaskIds array */

if (!isset($subTaskIds)) $subTaskIds = [];
$isLoop = $target_task && isset($subTaskIds[$target_task->id]);
if ($target_task) {
    $subTaskIds[$target_task->id] = $target_task->id;
}

if (!isset($key)) {
    $key = bin2hex(random_bytes(10));
}

$menu = [];
if ($is_editable && !$is_only_view && $can_edit) {

    if ($node) {
        if ($node->isAccessEdit($task, Yii::$app->user->identity)) {
            $menu[] = [
                'title' => 'Изменить', 'icon' => 'fas fa-pencil-alt', 'color' => '#0b458f',
                'click' => "showDialogProjectTreeEditNode(this, " . $task->id . ", " . $identifier->id . ", " . $node->id . ")",
            ];
        }

        if ($node->isAccessDelete($task, Yii::$app->user->identity)) {
            $menu[] = [
                'title' => 'Удалить', 'icon' => 'fas fa-trash', 'color' => '#ff0000',
                'click' => "deleteProjectTreeNode(this, " . $task->id . ", " . $identifier->id . ", " . $node->id . ")",
            ];
        }
    }
}

?>

    <div class="card card-small mb-0">
        <div class="py-1 px-2">
            <div style="display: flex; align-items: flex-start;">
                <div style="display: flex; align-items: baseline; gap: 5px;">
                    <div data-spoiler data-id="tree_node_link_<?= ($node->id ?? -1) . ($target_task->id ?? -1) ?>" data-save_time="60" data-container="li" data-content='[data-spoiler-content="<?= $key ?>"]'>
                        <?php if ($target_task && count($target_task->sub_tasks) > 0): ?>
                            <i class="far fa-plus-square mr-1" style="color: #9a9a9a;" data-close="1"></i>
                            <i class="far fa-minus-square mr-1" style="color: #9a9a9a; display: none" data-open="1"></i>
                        <?php endif; ?>

                        <?php if ($target_task): ?>
                            <a href="<?= Url::toRoute(['/process/task/view', 'id' => $target_task->id]) ?>" target="_blank"><?= $target_task->name ?></a>
                        <?php else: ?>
                            <span style="color: #ff1100">Задача удалена</span>
                        <?php endif; ?>
                    </div>
                    <?php if ($node && $is_editable && !$is_only_view && $can_edit): ?>
                        <i class="fas fa-arrows-alt js_drag_item" style="color: #999999; padding: 2px 7px; cursor: grab;"></i>
                    <?php endif; ?>
                </div>

                <?php if (count($menu) > 0): ?>
                    <div class="dropleft" style="margin-left: auto">
                        <button class="btn btn-xs btn-link ml-2" type="button" id="menu_<?= $key ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>
                        <div class="dropdown-menu" aria-labelledby="menu_<?= $key ?>">
                            <?php foreach ($menu as $item): ?>
                                <a href="javascript:void (0);" class="dropdown-item  <?= !isset($item['click']) ? "disabled" : "" ?>" onclick="<?= $item['click'] ?? "" ?>">
                                    <i class="<?= $item['icon'] ?> mr-1" style="color: <?= $item['color'] ?>; width: 16px; height: 16px"></i> <?= $item['title'] ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($target_task): ?>
                <?php if ($target_task->fm_id != 0): ?>
                    <span style="float: right; color: #b6b6b6; font-size: small"><?= $target_task->fm->fio ?? "-" ?></span>
                <?php endif; ?>

                <div style="font-style: italic; font-size: small">
                    <?php if ($target_task->template): ?>
                        <?= $target_task->template->name ?> <span style="font-size: small; color: #9e9e9e">(v<?= $target_task->version->version ?? "-" ?>)</span>
                    <?php else: ?>
                        <span style="color: #ff1100">БП удален</span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($target_task): ?>
            <div style="border-bottom-left-radius: 0.25rem;border-bottom-right-radius: 0.25rem; overflow: hidden">
                <?= $this->render('/task/view/progress/all', ['task' => $target_task]) ?>

                <?php if ($target_task->hasShowMinimalData()): ?>
                    <div class="py-1 px-2" style="font-size: small">
                        <?= $this->render('/task-data/view/data_minimal', ['task' => $target_task]) ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

<?php if ($target_task && count($target_task->sub_tasks) > 0): ?>
    <ul data-spoiler-content="<?= $key ?>">
        <?php if (!$isLoop): ?>
        <?php foreach ($target_task->sub_tasks as $sub_task): ?>
            <li>
                <?php $key2 = bin2hex(random_bytes(10)); ?>
                <?= $this->render('node_link', [
                    'task'        => $task,
                    'identifier'  => $identifier,
                    'node'        => null,
                    'target_task' => $sub_task->sub_task,
                    'key'         => $key2,

                    'is_editable'  => $is_editable,
                    'is_only_view' => $is_only_view,
                    'can_edit'     => $can_edit,

                    'subTaskIds' => $subTaskIds,
                ]); ?>
            </li>
        <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-default-danger">Зацикливание</div>
        <?php endif; ?>
    </ul>
<?php endif; ?>