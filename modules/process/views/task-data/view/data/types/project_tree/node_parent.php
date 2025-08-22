<?php

use app\models\Opers;
use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItemProjectTree;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $identifier Req3Identifiers */
/* @var $node Req3TasksDataItemProjectTree */


?>

<div class="card card-small mb-0" data-node="<?= $node->id ?>" style="min-width: 200px">
    <div class="py-1 px-2">
        <div>
            <?= $node->project_goal ?>
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


        </div>
    </div>

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
</div>