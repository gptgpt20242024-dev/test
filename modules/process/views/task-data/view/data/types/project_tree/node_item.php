<?php

use app\components\Str;
use app\models\Opers;
use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\Req3Corrections;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItemProjectTree;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $identifier Req3Identifiers */
/* @var $node Req3TasksDataItemProjectTree */
/* @var $children array */
/* @var $key array */

/* @var $is_editable boolean */
/* @var $is_only_view boolean */
/* @var $can_edit boolean */

if (!isset($key)) {
    $key = bin2hex(random_bytes(10));
}

$menu = [];

if ($is_editable && !$is_only_view && $can_edit) {

    if ($node->isAccessEdit($task, Yii::$app->user->identity)) {
        $menu[] = [
            'title' => 'Изменить', 'icon' => 'fas fa-pencil-alt', 'color' => '#0b458f',
            'click' => "showDialogProjectTreeEditNode(this, " . $task->id . ", " . $identifier->id . ", " . $node->id . ")",
        ];
    }

    if ($node->isAccessAddNode($task, Yii::$app->user->identity)) {
        $menu[] = [
            'title' => 'Добавить планируемую подзадачу', 'icon' => 'fas fa-plus', 'color' => '#bbbbbb',
            'click' => "showDialogProjectTreeAddNode(this, " . $task->id . ", " . $identifier->id . ", " . $node->id . ")",
        ];
        $menu[] = [
            'title' => 'Добавить подзадачей существующий БП', 'icon' => 'fas fa-link', 'color' => '#5b96f7',
            'click' => "showDialogProjectTreeAddNode(this, " . $task->id . ", " . $identifier->id . ", " . $node->id . ", " . Req3TasksDataItemProjectTree::NODE_TYPE_OTHER_TASK . ")",
        ];
    }

    if ($node->isAccessCreateTask($task, Yii::$app->user->identity)) {
        $menu[] = [
            'title' => 'Передать на исполнение в БП', 'icon' => 'fas fa-play', 'color' => '#21e100',
            'click' => "createTaskFromProjectTreeNode(this, " . $task->id . ", " . $identifier->id . ", " . $node->id . ")",
        ];
    }

    if ($node->isAccessDelete($task, Yii::$app->user->identity)) {
        $menu[] = [
            'title' => 'Удалить', 'icon' => 'fas fa-trash', 'color' => '#ff0000',
            'click' => "deleteProjectTreeNode(this, " . $task->id . ", " . $identifier->id . ", " . $node->id . ")",
        ];
    }

}

?>

<div class="card card-small mb-0" data-node="<?= $node->id ?>">
    <div class="py-1 px-2">
        <div style="display: flex; align-items: flex-start;">
            <div style="display: flex; align-items: baseline; gap: 5px;">

                <div>
                    <?= $node->project_goal ?>
                    <i class="fas fa-pen-fancy btn-correct text" onclick="showDialogCorrect(event, <?= $node->id ?>, <?= Req3Corrections::LINK_TYPE_NODE_PROJECT ?>)"></i>
                </div>

                <?php if (!empty($node->description)): ?>
                    <div style="display: none" data-node-description="<?= $node->id ?>">
                        <div style="word-break: break-word;">
                            <?= nl2br(Str::toLink($node->description)) ?>
                        </div>
                    </div>
                    <i class="fas fa-file-alt" style="cursor: pointer; color: #46a0ef;" title="Подробное описание задачи" onclick="showDialogProjectTreeNodeDescription(this, <?= $node->id ?>)"></i>
                <?php endif; ?>

                <?php if ($is_editable && !$is_only_view && $can_edit): ?>
                    <i class="fas fa-arrows-alt js_drag_item" style="color: #999999; padding: 2px 7px; cursor: grab;"></i>
                <?php endif; ?>
            </div>

            <?php if (count($menu) > 0): ?>
                <div class="dropleft" style="margin-left: auto">
                    <button class="btn btn-xs btn-link ml-2" type="button" id="menu_<?= $key ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>
                    <div class="dropdown-menu" aria-labelledby="menu_<?= $key ?>" style="max-width: 400px;">

                        <a class="dropdown-item disabled" style="background-color: #ededed; text-overflow: ellipsis; overflow: hidden"><?= $node->project_goal ?></a>

                        <?php foreach ($menu as $item): ?>
                            <a href="javascript:void (0);" class="dropdown-item  <?= !isset($item['click']) ? "disabled" : "" ?>" onclick="<?= $item['click'] ?? "" ?>">
                                <i class="<?= $item['icon'] ?> mr-1" style="color: <?= $item['color'] ?>; width: 16px; height: 16px"></i> <?= $item['title'] ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div style="font-size: small">

            <?php if (!empty($node->planned_work_costs) || !empty($node->actual_work_costs)): ?>
                <div>
                    <span style="font-weight: bold; color: #0c5460">Трудозатраты:</span>
                    <?php if (!empty($node->planned_work_costs)): ?>
                        <?= $node->planned_work_costs ?>
                    <?php endif; ?>
                    <?php if (!empty($node->actual_work_costs)): ?>
                        <span style="color: #0f7f92">(факт.: <?= $node->actual_work_costs ?>)</span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($node->planned_income) || !empty($node->actual_income)): ?>
                <div>
                    <span style="font-weight: bold; color: #0c5460">Доходы</span>
                    <?php if (!empty($node->planned_income_type)): ?>
                        <span style="color: #8a8a8a">(<?= Req3TasksDataItemProjectTree::getTypeName($node->planned_income_type) ?>)</span>
                    <?php endif; ?>
                    :
                    <?php if (!empty($node->planned_income)): ?>
                        <?= $node->planned_income ?>
                    <?php endif; ?>
                    <?php if (!empty($node->actual_income)): ?>
                        <span style="color: #0f7f92">(факт.: <?= $node->actual_income ?>)</span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($node->planned_expenses) || !empty($node->actual_expenses)): ?>
                <div>
                    <span style="font-weight: bold; color: #0c5460">Расходы</span>
                    <?php if (!empty($node->planned_expenses_type)): ?>
                        <span style="color: #8a8a8a">(<?= Req3TasksDataItemProjectTree::getTypeName($node->planned_expenses_type) ?>)</span>
                    <?php endif; ?>
                    :
                    <?php if (!empty($node->planned_expenses)): ?>
                        <?= $node->planned_expenses ?>
                    <?php endif; ?>
                    <?php if (!empty($node->actual_expenses)): ?>
                        <span style="color: #0f7f92">(факт.: <?= $node->actual_expenses ?>)</span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($node->deadline)): ?>
                <div>
                    <span style="font-weight: bold; color: #0c5460">Дедлайн:</span> <?= $node->deadline ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($node->oper_id)): ?>
                <div>
                    <span style="font-weight: bold; color: #0c5460">Исполнитель:</span> <?= Opers::getFioOrFioDeletedHtml($node) ?> (<?= $node->getRole()->description ?? "-" ?>)
                </div>
            <?php endif; ?>

            <?php if (!empty($node->status) && empty($node->target_task_id)): ?>
                <div>
                    <span style="font-weight: bold; color: #0c5460">Состояние:</span> <?= Req3TasksDataItemProjectTree::getStatusName($node->status) ?>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <div style="border-bottom-left-radius: 0.25rem;border-bottom-right-radius: 0.25rem; overflow: hidden">
        <?php if (empty($node->parent_id)): ?>
            <div class="py-1 px-2" style="font-size: small">
                <div>
                    <a href="<?= Url::toRoute(['/process/task/view', 'id' => $node->from_task_id]) ?>" target="_blank">Задача корневая</a>
                </div>
                <?php if ($node->from_task): ?>
                    <?php if ($node->from_task->step): ?>
                        <div>
                            <span style="font-weight: bold; color: #0c5460">Статус:</span>
                            <?php if ($node->from_task->step->is_first): ?>
                                План - первый шаг
                            <?php elseif ($node->from_task->step->is_auto): ?>
                                Пауза - автоматический шаг
                            <?php elseif ($node->from_task->step->is_deviation): ?>
                                Отклонение - шаг отклонения
                            <?php elseif ($node->from_task->step->is_deviation_architect): ?>
                                Отклонение - шаг отклонения архитектора
                            <?php elseif ($node->from_task->step->is_last): ?>
                                Закрыто  - последний шаг бп
                            <?php else: ?>
                                В работе - активная задача
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <?php if ($node->from_task) echo $this->render('/task/view/progress/time', ['task' => $node->from_task]) ?>
        <?php endif; ?>

        <?php if (!empty($node->target_task_id)): ?>
            <div class="py-1 px-2" style="font-size: small">
                <div>
                    <a href="<?= Url::toRoute(['/process/task/view', 'id' => $node->target_task_id]) ?>" target="_blank">Исполняемая задача</a>
                </div>
                <?php if ($node->target_task): ?>
                    <?php if ($node->target_task->step): ?>
                        <div>
                            <span style="font-weight: bold; color: #0c5460">Статус:</span>
                            <?php if ($node->target_task->step->is_first): ?>
                                План - первый шаг
                            <?php elseif ($node->target_task->step->is_auto): ?>
                                Пауза - автоматический шаг
                            <?php elseif ($node->target_task->step->is_deviation): ?>
                                Отклонение - шаг отклонения
                            <?php elseif ($node->target_task->step->is_deviation_architect): ?>
                                Отклонение - шаг отклонения архитектора
                            <?php elseif ($node->target_task->step->is_last): ?>
                                Закрыто  - последний шаг бп
                            <?php else: ?>
                                В работе - активная задача
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <?php if ($node->target_task) echo $this->render('/task/view/progress/time', ['task' => $node->target_task]) ?>
        <?php endif; ?>

        <?php if (count($children) > 0): ?>
            <div class="py-1 px-2" style="background-color: #b8e9c3;" data-spoiler data-id="tree_node_<?= $node->id ?>" data-save_time="60" data-container="li" data-content='[data-spoiler-content="<?= $key ?>"]'>
                <i class="far fa-plus-square mr-1" style="color: #9a9a9a;" data-close="1"></i>
                <i class="far fa-minus-square mr-1" style="color: #9a9a9a; display: none" data-open="1"></i>
                Есть подзадачи
            </div>
        <?php endif; ?>


    </div>


</div>