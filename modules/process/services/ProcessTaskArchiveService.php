<?php

namespace app\modules\process\services;

use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_archive\TaskArchive;
use app\modules\process\models\task_archive\TaskArchiveEntity;
use app\modules\process\models\task_data\Req3TasksDataItems;
use app\modules\process\models\template_steps\Req3TemplateSteps;
use DateTimeImmutable;
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

        if (empty($dataItems)) {
            Yii::warning("Задача {$task->id} не содержит данных для архивирования", __METHOD__);
            return false;
        }

        $db = TaskArchive::getDb();
        $transaction = $db->beginTransaction();
        try {
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
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::error("Ошибка архивации задачи {$task->id}: {$e->getMessage()}", __METHOD__);
            return false;
        }

        return (bool)$task->delete();
    }

    protected function collectTaskData(Req3Tasks $task): array
    {
        return [];
    }

    public function archiveAvailableTasks(): void
    {
        foreach ($this->findTasksForArchive() as $task) {
            $this->archiveTask($task);
        }
    }

}