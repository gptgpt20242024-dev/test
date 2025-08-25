<?php

namespace app\modules\process\widgets;

use app\components\Date;
use app\models\Opers;
use app\modules\knowledge_base\services\ChatTaskLinkService;
use app\modules\order\models\TaskProcessLinks;
use app\modules\process\constants\IdentifierCompleteErrors;
use app\modules\process\dto\RuleDataDto;
use app\modules\process\models\_query\Req3TasksDataItemsQuery;
use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItemIdentifierComments;
use app\modules\process\models\task_data\Req3TasksDataItemProjectTree;
use app\modules\process\models\task_data\Req3TasksDataItems;
use app\modules\process\services\ProcessTaskService;
use Yii;
use yii\base\Widget;

class TaskHeaderWidget extends Widget
{
    public Req3Tasks   $task;
    public RuleDataDto $ruleData;
    public array       $notCompleteIdentifiers;

    public function run()
    {
        $parentProjectTaskIds = Req3TasksDataItemProjectTree::find()->alias('node')->andWhere(['node.target_task_id' => $this->task->id])
            ->innerJoinWith(['root.item' => function (Req3TasksDataItemsQuery $query) {
                $query->andOnCondition(['type' => Req3Identifiers::TYPE_PROJECT_TREE]);
                $query->andOnCondition(['link_type' => Req3TasksDataItems::LINK_TYPE_TASK]);
                $query->andOnCondition(['is_deleted' => 0]);

                $query->innerJoinWith('task task', false);
            }], false)
            ->select(['task.id'])->column();

        $parentProjectTasks = !empty($parentProjectTaskIds) ? Req3Tasks::find()->id($parentProjectTaskIds)->all() : [];

        $dataWorkCheck = Req3TasksDataItemIdentifierComments::getIdentifiersAndWorksCheckWork();
        $hasTaskCheck = [
            'step' => $dataWorkCheck['step_ids'][$this->task->step_id] ?? null,
            'work' => $dataWorkCheck['work_rated_ids'][$this->task->step->work_rated_id ?? null] ?? null,
        ];

        /** @var TaskProcessLinks $orderLink */
        $orderLink = TaskProcessLinks::find()->andWhere(['process_id' => $this->task->id])->one();
        $orderProjectLinks = [];
        if ($orderLink) {
            $orderProjectLinks = TaskProcessLinks::find()->andWhere(['project_id' => $orderLink->project_id])->all();
        }

        $processService = Yii::$container->get(ProcessTaskService::class);
        $rule2 = $this->ruleData->rule;
        $functionsBtn = $this->ruleData->functionsBtn;

        $isExceededTransitions = $rule2 && $rule2->isExceededTransitions($this->task);

        $nextStepSetting = [
            'haveTransitions' => $processService->isHaveTransitionsNext($this->task->step_id),
            'rule2_id'    => $rule2->id ?? null,
            'access'      => true,
            'arrow_class' => $this->task->isLastStep() ? "btn-secondary" : "btn-primary",
            'class'       => $this->task->isLastStep() ? "btn-secondary" : "btn-primary",
            'icon'        => false,
            'error'       => null,
            'error_color' => null,
            'hint'        => null,
        ];

        if ($this->task->step->block_by_oper_id ?? null) {
            $nextStepSetting['access'] = false;
            $nextStepSetting['icon'] = "fas fa-tools text-danger";
            $nextStepSetting['error'] = "Сервисный режим: " . Opers::getFioOrFioDeletedHtml($this->task->step, 'blockOper', 'block_by_oper_id');
            $nextStepSetting['error_color'] = "#930000";
            $nextStepSetting['class'] = "btn-warning";
            $nextStepSetting['arrow_class'] = "btn-danger";
        } elseif ($nextStepSetting['haveTransitions']) {
            if (count($this->notCompleteIdentifiers) > 0) {
                $nextStepSetting['access'] = false;
                $nextStepSetting['icon'] = "fas fa-file-signature";
                $nextStepSetting['error'] = "Ошибка проверки данных";
                $nextStepSetting['error_color'] = "#930000";
                $nextStepSetting['class'] = "btn-warning";
                $nextStepSetting['arrow_class'] = "btn-warning";
                if (isset($this->notCompleteIdentifiers[IdentifierCompleteErrors::ERROR_COMPLETE_FILL])) {
                    $nextStepSetting['error'] = "Не все данные заполнены";
                }
                if (isset($this->notCompleteIdentifiers[IdentifierCompleteErrors::ERROR_COMPLETE_REMARKS])) {
                    $nextStepSetting['error'] = "Не все улучшения исправлены";
                }
            } elseif (!$this->task->isAccessActionNextStep(Yii::$app->user->id)) {
                $nextStepSetting['access'] = false;
                $nextStepSetting['icon'] = "fas fa-exclamation-triangle";
                $nextStepSetting['error'] = "У вас нет прав переключать шаг";
                $nextStepSetting['error_color'] = "#ffb3b3";
            } elseif (!$rule2) {
                $nextStepSetting['access'] = false;
                $nextStepSetting['icon'] = "fas fa-random";
                $nextStepSetting['error'] = "Ни один маршрут не найден";
                $nextStepSetting['error_color'] = "#ffcfcf";
                $nextStepSetting['class'] = "btn-danger";
                $nextStepSetting['arrow_class'] = "btn-danger";
            } elseif (!$rule2->isAccessAction(Yii::$app->user->id, $this->task)) {
                $access = [];
                if ($rule2->is_available_only_controller)
                    $access[] = "Ответственным";
                if ($rule2->is_available_only_executor)
                    $access[] = "Исполнителям";
                if ($rule2->is_available_only_worker)
                    $access[] = "Действующим";
                $nextStepSetting['access'] = false;
                $nextStepSetting['icon'] = "fas fa-exclamation-triangle";
                $nextStepSetting['error'] = "Маршрут ограничен, доступен только: " . implode(", ", $access);
                $nextStepSetting['error_color'] = "#ff9292";
            } elseif ($isExceededTransitions) {
                if (!empty($rule2->to_step_limit_id)) {
                    $real_minutes = $rule2->real_minutes;
                    if ($real_minutes == 0) {
                        $real_minutes = $this->task->step ? $this->task->step->getRealMinutes() : 0;
                    }
                    $nextStepSetting['hint'] = ($rule2->toStepLimit->name ?? "-") . " (" . Date::minutesToText($real_minutes, 1, true) . ")";
                } else {
                    $nextStepSetting['access'] = false;
                    $nextStepSetting['icon'] = "fas fa-redo";
                    $nextStepSetting['error'] = "Достигнут лимит перехода по маршруту";
                    $nextStepSetting['error_color'] = "#ffcfcf";
                    $nextStepSetting['class'] = "btn-danger";
                    $nextStepSetting['arrow_class'] = "btn-danger";
                }
            } else {
                if ($rule2->is_deviation) {
                    $nextStepSetting['class'] = "btn-danger";
                    $nextStepSetting['arrow_class'] = "btn-danger";
                    $nextStepSetting['icon'] = "fas fa-biohazard";
                }

                $real_minutes = $rule2->real_minutes;
                if ($real_minutes == 0) {
                    $real_minutes = $this->task->step ? $this->task->step->getRealMinutes() : 0;
                }
                $nextStepSetting['hint'] = ($rule2->toStep->name ?? "-") . " (" . Date::minutesToText($real_minutes, 1, true) . ")";
            }
        }

        $subTasks = [
            'active' => ['name' => "Активные", 'tasks' => []],
            'auto'   => ['name' => "Автоматические", 'tasks' => []],
            'last'   => ['name' => "Закрытые", 'tasks' => []],
            'other'  => ['name' => "Удаленные", 'tasks' => []],
        ];

        foreach ($this->task->sub_tasks as $sub_task) {
            if ($sub_task->sub_task->step ?? false) {
                if ($sub_task->sub_task->step->is_last)
                    $subTasks['last']['tasks'][] = $sub_task;
                elseif ($sub_task->sub_task->step->is_auto)
                    $subTasks['auto']['tasks'][] = $sub_task;
                else
                    $subTasks['active']['tasks'][] = $sub_task;
            } else
                $subTasks['other']['tasks'][] = $sub_task;
        }
        $subTasks = array_filter($subTasks, fn($it) => count($it['tasks']) > 0);

        $chatTaskLinkService = Yii::$container->get(ChatTaskLinkService::class);
        $chatsCount = $chatTaskLinkService->countByTaskId($this->task->id);

        return $this->render('task_header', [
            'task'                   => $this->task,
            'rule2'                  => $rule2,
            'isExceededTransitions'  => $isExceededTransitions,
            'notCompleteIdentifiers' => $this->notCompleteIdentifiers,
            'functionsBtn'           => $functionsBtn,
            'nextStepSetting'        => $nextStepSetting,
            'orderProjectLinks'      => $orderProjectLinks,
            'parentProjectTasks'     => $parentProjectTasks,
            'hasTaskCheck'           => $hasTaskCheck,
            'subTasks'               => $subTasks,
            'chatsCount'             => $chatsCount,
        ]);
    }
}