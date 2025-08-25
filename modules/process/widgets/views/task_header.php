<?php

use app\modules\order\models\TaskProcessLinks;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\template_steps\Req3TemplateStepRule2;
use app\modules\process\models\template_steps\Req3TemplateStepRule2Functions;
use app\modules\process\models\template_steps\Req3TemplateStepRuleFunctions;

/* @var $this yii\web\View */

/* @var $task Req3Tasks */
/* @var $nextStepSetting array */
/* @var $orderProjectLinks TaskProcessLinks[] */
/* @var $parentProjectTasks Req3Tasks[] */
/* @var $hasTaskCheck array */
/* @var $notCompleteIdentifiers array */
/* @var $isExceededTransitions bool */
/* @var $rule2 Req3TemplateStepRule2 */
/* @var $functionsBtn Req3TemplateStepRule2Functions[] */
/* @var $subTasks array */
/* @var $chatsCount int */
?>

<div data-remark-step="1"></div>

<div class="card" data-block="header">
    <?php if ($task->parent_task): ?>
        <div class="card-header">
            <?= $this->render('task_header/links/parent', ['task' => $task]) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($parentProjectTasks)): ?>
        <div class="card-header">
            <div style="font-size: small; color: #808080">Родительские задачи в дереве проектов которых участвует данная задача:</div>
            <?php foreach ($parentProjectTasks as $parentProjectTask): ?>
                <?= $this->render('task_header/links/parent_node', ['task' => $parentProjectTask]) ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="card-header">
        <?= $this->render('task_header/name_and_info', ['task' => $task]) ?>
    </div>

    <div class="card-footer p-3">
        <div style="display: flex; gap: 15px; flex-direction: column">
            <?= $this->render('task_header/step_and_other', ['task' => $task, 'nextStepSetting' => $nextStepSetting, 'chatsCount' => $chatsCount]) ?>

            <?= $this->render('task_header/information_alert', [
                'task'                   => $task,
                'rule2'                  => $rule2,
                'hasTaskCheck'           => $hasTaskCheck,
                'notCompleteIdentifiers' => $notCompleteIdentifiers,
                'isExceededTransitions' => $isExceededTransitions,
                'nextStepSetting'       => $nextStepSetting,
            ]); ?>

            <?= $this->render('task_header/functions_btns', ['task' => $task, 'functionsBtn' => $functionsBtn]) ?>
        </div>
    </div>

    <?php if (count($task->sub_tasks) > 0): ?>
        <div class="card-footer p-3" style="background-color: #e7e7e7;">
            <?= $this->render('task_header/links/sub_tasks', ['task' => $task, 'subTasks' => $subTasks]) ?>
        </div>
    <?php endif; ?>

    <?php if (count($task->started_orders) > 0): ?>
        <div class="card-footer p-3" style="background-color: #dadada;">
            <?= $this->render('task_header/links/sub_orders', ['task' => $task]) ?>
        </div>
    <?php endif; ?>
</div>




