<?php

namespace app\modules\process\services;

use app\modules\order\models\TaskProcessLinks;
use app\modules\process\factories\ArchiveDataDtoFactory;
use app\modules\process\models\_query\Req3TasksDataItemsQuery;
use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\Req3FunctionBase;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task\Req3TasksStepHistory;
use app\modules\process\models\task_archive\TaskArchive;
use app\modules\process\models\task_archive\TaskArchiveEntity;
use app\modules\process\models\task_data\Req3TasksDataItemProjectTree;
use app\modules\process\models\task_data\Req3TasksDataItems;
use app\modules\process\models\task_opers\Req3TaskOperOnline;
use app\modules\process\models\template_steps\Req3TemplateSteps;
use DateTimeImmutable;
use Throwable;
use Yii;
use yii\db\Exception;
use yii\db\Expression;

class ProcessTaskArchiveService
{
    /**
     * @return Req3Tasks[]
     */
    public function findTasksForArchive(): array
    {
        $today = new DateTimeImmutable('today');

        $steps = Req3TemplateSteps::find()
            ->select(['id', 'delete_after_days'])
            ->where(['not', ['delete_after_days' => null]])
            ->asArray()
            ->all();

        if (empty($steps)) {
            return [];
        }

        $condition = ['or'];
        foreach ($steps as $step) {
            $threshold = $today->modify('-' . $step['delete_after_days'] . ' days')->format('Y-m-d');
            $condition[] = ['and',
                ['req3_tasks.step_id' => $step['id']],
                ['<=', 'req3_tasks.date_start_step', $threshold],
            ];
        }

        if (count($condition) === 1) {
            return [];
        }

        return Req3Tasks::find()->with(['step.template'])->andWhere($condition)->all();
    }

    public function archiveTask(Req3Tasks $task): bool
    {
        $archiveIdentifiers = $task->version->setting['archiveIdentifiers'] ?? [];

        /** @var Req3TasksDataItems[] $dataItems */
        $dataItems = [];
        foreach ($archiveIdentifiers as $identifierId) {
            $items = $task->getDataIdentifier($identifierId, true, true);
            if (!empty($items)) {
                foreach ($items as $item) {
                    $key = $item->value_id . ':' . $item->type;
                    $dataItems[$key] = $item;
                }
            }
        }

        /*if (empty($dataItems)) {
            Yii::warning("Задача {$task->id} не содержит данных для архивирования", __METHOD__);
            return false;
        }*/

        $db = TaskArchive::getDb();
        $transaction = $db->beginTransaction();
        try {
            TaskArchive::deleteAll(['task_id'=>$task->id]);
            TaskArchiveEntity::deleteAll(['task_id'=>$task->id]);

            $archive = new TaskArchive();
            $archive->task_id = $task->id;
            $archive->task_name = $task->name;
            $archive->template_id = $task->template_id;
            $archive->template_name = $task->step->template->name ?? '';
            $archive->task_date_create = $task->create_date;
            $archive->task_date_start_step = $task->date_start_step;
            $archive->date_add_to_archive = new Expression('NOW()');
            $archive->step_is_last = (int)$task->step->is_last ?? 0;
            $archive->step_last_status = $task->step->last_status ?? 0;
            $archive->data_json = json_encode($this->collectTaskData($task), JSON_UNESCAPED_UNICODE);
            if (!$archive->save()) {
                throw new Exception("TaskArchive: " . implode(", ", $archive->getFirstErrors()));
            }

            foreach ($dataItems as $item) {
                $link = new TaskArchiveEntity();
                $link->task_id = $task->id;
                $link->value_id = $item->value_id;
                $link->identifier_type = $item->type;
                if (!$link->save()) {
                    throw new Exception("TaskArchiveEntity: " . implode(", ", $archive->getFirstErrors()));
                }
            }
            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();
            Yii::error("Ошибка архивации задачи {$task->id}: {$e->getMessage()}", __METHOD__);
            return false;
        }

        //return (bool)$task->delete();
        return true;
    }

    protected function collectTaskData(Req3Tasks $task): array
    {
        $history = $this->collectTaskHistory($task);
        $relations      = $this->collectTaskRelations($task);

        $timeExecute = $task->getTimeExecute();
        $deviationInfo = $task->getDeviationInfo();
        $timeTemplate = ($task->version->execute_minutes ?? 0) * 60;

        $dataItems = [];
        foreach ($task->data as $item) {
            $dataItems[] = ArchiveDataDtoFactory::serializeDataItem($item);
        }

        return [
            'history'          => $history,
            'time_execute'   => $timeExecute,
            'deviation_info' => $deviationInfo,
            'time_template'  => $timeTemplate,
            'data_items'     => $dataItems,
            'relations'     => $relations,
        ];
    }

    protected function collectTaskHistory(Req3Tasks $task): array
    {
        $items = [];
        $before_full_info_transitions = false;
        foreach ($task->step_history as $history) {
            if (!$before_full_info_transitions) {
                $items[] = $this->buildTransitionItem($history);
            }
            $before_full_info_transitions = false;

            $data = $history->getDataArray();

            $items[] = $this->buildStepItem($task, $history, $data);

            $stepItems = array_merge(
                $this->buildTransitionDetailItems($history, $data, $before_full_info_transitions),
                $this->buildInfoItems($data),
                $this->buildLinkItems($data),
                $this->buildFunctionItems($history, $data, $before_full_info_transitions),
                $this->buildDataChangeItems($history, $data)
            );

            usort($stepItems, function ($a, $b) {
                if ($a['time'] != $b['time']) {
                    return $a['time'] <=> $b['time'];
                }
                if ($a['priority'] != $b['priority']) {
                    return $a['priority'] <=> $b['priority'];
                }
                if (isset($a['n']) && isset($b['n'])) {
                    return $a['n'] <=> $b['n'];
                }
                return 0;
            });

            foreach ($stepItems as $itemStep) {
                $items[] = $itemStep;
            }
        }
        return $items;
    }

    protected function collectTaskRelations(Req3Tasks $task): array
    {
        $relations = [];

        if ($task->crash_link) {
            $relations['crash'] = [
                'crash_id' => $task->crash_link->crash_id,
            ];
        }

        /** @var TaskProcessLinks $orderLink */
        $orderLink = TaskProcessLinks::find()->andWhere(['process_id' => $task->id])->one();
        if ($orderLink) {
            $relations['order'] = [
                'task_id' => $orderLink->task_id,
                'project_process_ids' => TaskProcessLinks::find()->select('process_id')->andWhere(['project_id' => $orderLink->project_id])->column(),
            ];
        }

        if ($task->started_from_email) {
            $relations['email'] = [
                'from' => $task->started_from_email->from,
                'date' => $task->started_from_email->date_email_received,
            ];
        }

        if ($task->parent_task) {
            $parentTask = $task->parent_task->task ?? null;
            $relations['parent_task'] = [
                'task_id' => $task->parent_task->task_id,
                'name' => $parentTask->name ?? null,
            ];
        }

        $parentProjectTaskIds = Req3TasksDataItemProjectTree::find()->alias('node')
            ->andWhere(['node.target_task_id' => $task->id])
            ->innerJoinWith(['root.item' => function (Req3TasksDataItemsQuery $query) {
                $query->andOnCondition(['type' => Req3Identifiers::TYPE_PROJECT_TREE]);
                $query->andOnCondition(['link_type' => Req3TasksDataItems::LINK_TYPE_TASK]);
                $query->andOnCondition(['is_deleted' => 0]);
                $query->innerJoinWith('task task', false);
            }], false)
            ->select(['task.id'])->column();
        if (!empty($parentProjectTaskIds)) {
            $relations['parent_project_tasks'] = [];
            $parentProjectTasks = Req3Tasks::find()->id($parentProjectTaskIds)->all();
            foreach ($parentProjectTasks as $parentProjectTask) {
                $relations['parent_project_tasks'][] = [
                    'id' => $parentProjectTask->id,
                    'name' => $parentProjectTask->name,
                ];
            }
        }

        if (count($task->sub_tasks) > 0) {
            foreach ($task->sub_tasks as $subTask) {
                $sub = $subTask->sub_task;
                $info = [
                    'id' => $subTask->sub_task_id,
                    'name' => $sub->name ?? null,
                ];
                if ($sub) {
                    if ($sub->step->is_last ?? false) {
                        $relations['sub_tasks']['last'][] = $info;
                    } elseif ($sub->step->is_auto ?? false) {
                        $relations['sub_tasks']['auto'][] = $info;
                    } else {
                        $relations['sub_tasks']['active'][] = $info;
                    }
                } else {
                    $relations['sub_tasks']['other'][] = $info;
                }
            }
        }

        if (count($task->started_orders) > 0) {
            foreach ($task->started_orders as $order) {
                $relations['sub_orders'][] = [
                    'order_id' => $order->order_id,
                ];
            }
        }

        return $relations;
    }

    protected function buildTransitionItem(Req3TasksStepHistory $history): array
    {
        return [
            'time'     => strtotime($history->start_date),
            'type'     => 'transition',
            'priority' => 4,
            'item'     => [
                'start_date'     => $history->start_date,
                'oper_id'        => $history->oper_id,
                'from_task_id'   => $history->from_task_id,
                'from_task_name' => $history->from_task ? $history->from_task->name : null,
            ],
        ];
    }

    protected function buildStepItem(Req3Tasks $task, Req3TasksStepHistory $history, array $data): array
    {
        $labelObj = empty($history->end_date) ? ($task->queue_label ?? null) : ($history->queue_label ?? null);
        $step = $history->step;
        $stepData = [
            'start_date' => $history->start_date,
            'end_date'   => $history->end_date,
            'step_id'    => $history->step_id,
            'step_name'  => $step ? $step->name : null,
            'step_is_first' => $step && $step->is_first,
            'step_is_auto'  => $step && $step->is_auto,
            'step_is_calls' => $step && $step->is_calls,
            'step_is_last'  => $step && $step->is_last,
            'step_is_deviation' => $step && $step->isDeviation(),
            'escalation' => $history->escalation,
            'is_overdue' => (bool)$history->is_overdue,
            'is_deviation_job_complete' => $history->isDeviationJobComplete(),
        ];
        if ($labelObj) {
            $stepData['label'] = [
                'label' => $labelObj->label,
                'color' => $labelObj->color,
            ];
        }

        $stepItem = [
            'time'     => strtotime($history->start_date),
            'type'     => 'step',
            'priority' => 5,
            'item'     => $stepData,
            'online'   => [],
        ];

        if (empty($history->end_date)) {
            $data['online'] = Req3TaskOperOnline::find()
                ->select(new Expression("SUM(online_seconds) seconds"))
                ->andWhere(['task_id' => $task->id, 'step_id' => $task->step_id])
                ->groupBy(['oper_id'])
                ->indexBy('oper_id')
                ->column();
        }

        if (isset($data['online'])) {
            foreach ($data['online'] as $oper_id => $seconds) {
                if (is_array($seconds)) {
                    $oper_id = $seconds['oper_id'];
                    $seconds = $seconds['seconds'];
                }
                if (!isset($stepItem['online'][$oper_id])) {
                    $stepItem['online'][$oper_id] = 0;
                }
                $stepItem['online'][$oper_id] += $seconds;
            }
        }

        return $stepItem;
    }

    protected function buildTransitionDetailItems(Req3TasksStepHistory $history, array $data, bool &$before_full_info_transitions): array
    {
        $items = [];

        if (isset($data['transitions'])) {
            foreach ($data['transitions'] as $transition) {
                if (!isset($transition['time_start'])) {
                    continue;
                }

                if (array_key_exists('rule2_id', $transition)) {
                    $itemTransition = [
                        'time_start' => $transition['time_start'],
                        'oper_id'    => $transition['oper_id'] ?? null,
                        'rule2_id'   => $transition['rule2_id'],
                        'from_step_id' => $history->step_id,
                    ];
                    if (isset($transition['triggeredRuleIds'])) {
                        $itemTransition['triggeredRuleIds'] = $transition['triggeredRuleIds'];
                    }
                    if (isset($transition['ok'])) {
                        $itemTransition['ok'] = $transition['ok'];
                    }
                    $items[] = [
                        'time'     => $transition['time_start'],
                        'type'     => 'rule2_detail',
                        'priority' => 4,
                        'item'     => $itemTransition,
                    ];
                } else {
                    $itemTransition = [
                        'time_start' => $transition['time_start'],
                        'oper_id'    => $transition['oper_id'] ?? null,
                    ];
                    if (isset($transition['transition']['name'])) {
                        $itemTransition['transition'] = ['name' => $transition['transition']['name']];
                    }
                    if (isset($transition['ok'])) {
                        $itemTransition['ok'] = $transition['ok'];
                    }
                    $items[] = [
                        'time'     => $transition['time_start'],
                        'type'     => 'transition_detail',
                        'priority' => 4,
                        'item'     => $itemTransition,
                    ];
                }

                $before_full_info_transitions = true;
            }
        }

        return $items;
    }

    protected function buildInfoItems(array $data): array
    {
        $items = [];

        if (isset($data['info'])) {
            foreach ($data['info'] as $info) {
                $items[] = [
                    'time'     => $info['time_start'],
                    'type'     => 'info',
                    'priority' => 0,
                    'item'     => [
                        'time_start' => $info['time_start'],
                        'message'    => $info['message'] ?? '',
                    ],
                ];
            }
        }

        return $items;
    }

    protected function buildLinkItems(array $data): array
    {
        $items = [];

        if (isset($data['link'])) {
            foreach ($data['link'] as $link) {
                $linkItem = [
                    'time_start' => $link['time_start'],
                    'type'       => $link['type'] ?? 'link',
                    'oper_id'    => $link['oper_id'] ?? null,
                ];
                if (isset($link['child_id'])) {
                    $linkItem['child_id'] = $link['child_id'];
                }
                if (isset($link['parent_id'])) {
                    $linkItem['parent_id'] = $link['parent_id'];
                }
                $items[] = [
                    'time'     => $link['time_start'],
                    'type'     => 'link',
                    'priority' => 0,
                    'item'     => $linkItem,
                ];
            }
        }

        return $items;
    }

    protected function buildFunctionItems($history, array $data, bool &$before_full_info_transitions): array
    {
        $items = [];

        if (isset($data['functions'])) {
            foreach ($data['functions'] as $i => $function) {
                $functionData = [
                    'time_start' => $function['time_start'],
                    'type'       => $function['type'] ?? Req3FunctionBase::TYPE_NEXT_STEP,
                ];
                if (isset($function['name'])) {
                    $functionData['name'] = $function['name'];
                }
                if (isset($function['oper_id'])) {
                    $functionData['oper_id'] = $function['oper_id'];
                }
                if (isset($function['btn_name'])) {
                    $functionData['btn_name'] = $function['btn_name'];
                }
                if (!empty($function['data'])) {
                    $functionData['data'] = $function['data'];
                }
                if (!empty($function['errors'])) {
                    $functionData['errors'] = $function['errors'];
                }

                $functionItem = [
                    'time'     => $function['time_start'],
                    'type'     => 'function',
                    'priority' => 2,
                    'n'        => $i,
                    'item'     => $functionData,
                ];

                if (!$before_full_info_transitions) {
                    if (($functionData['type'] ?? Req3FunctionBase::TYPE_NEXT_STEP) == Req3FunctionBase::TYPE_NEXT_STEP && $history->end_date != null) {
                        $functionItem['time'] = strtotime($history->end_date);
                    }
                }

                $items[] = $functionItem;
            }
        }

        return $items;
    }

    protected function buildDataChangeItems($history, array $data): array
    {
        $items = [];

        if (isset($data['data_change'])) {
            foreach ($data['data_change'] as $dataItem) {
                $time = $dataItem['time'] ?? strtotime($history->start_date);
                $itemData = [
                    'time'  => $time,
                    'name'  => $dataItem['name'] ?? '',
                    'value' => $dataItem['value'] ?? [],
                ];
                if (isset($dataItem['oper_id'])) {
                    $itemData['oper_id'] = $dataItem['oper_id'];
                }
                $items[] = [
                    'time'     => $time,
                    'type'     => 'data',
                    'priority' => 3,
                    'item'     => $itemData,
                ];
            }
        }

        return $items;
    }

    public function archiveAvailableTasks(): void
    {
        foreach ($this->findTasksForArchive() as $task) {
            $this->archiveTask($task);
        }
    }

}