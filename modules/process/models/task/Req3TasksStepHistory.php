<?php

namespace app\modules\process\models\task;

use app\models\Opers;
use app\modules\process\models\_query\Req3TasksStepHistoryQuery;
use app\modules\process\models\Req3QueueLabels;
use app\modules\process\models\template_steps\Req3TemplateStepRule2;
use app\modules\process\models\template_steps\Req3TemplateSteps;
use Exception;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "req3_tasks_step_history".
 *
 * @property integer                        $id
 * @property integer                        $from_task_id
 * @property integer                        $task_id
 * @property integer                        $oper_id
 * @property integer                        $close_oper_id
 * @property integer                        $from_step_id
 * @property integer                        $step_id
 * @property integer               $rule2_id
 * @property string                         $start_date
 * @property string                         $end_date
 * @property string                         $data_json
 * @property integer                        $is_overdue
 * @property double                         $priority_value
 * @property integer                        $escalation
 * @property integer                        $queue_label_id
 *
 * @property Req3Tasks                      $from_task
 * @property Req3Tasks                      $task
 * @property Req3TemplateSteps              $from_step
 * @property Req3TemplateSteps              $step
 * @property Req3TemplateStepRule2 $rule2
 * @property Opers                          $oper
 * @property Opers                          $close_oper
 * @property Req3QueueLabels                $queue_label
 */
class Req3TasksStepHistory extends ActiveRecord
{
    // ============================================================================
    // ============================== КОНСТАНТЫ ===================================
    // ============================================================================

    // ============================================================================
    // ============================== ДОПОЛНИТЕЛЬНЫЕ ПОЛЯ =========================
    // ============================================================================

    // ============================================================================
    // ============================== ИНИТ ========================================
    // ============================================================================
    public function init()
    {
    }

    public static function tableName()
    {
        return 'req3_tasks_step_history';
    }

    public function attributeLabels()
    {
        return [
            'id'             => 'ID',
            'from_task_id'   => 'From Task ID',
            'task_id'        => 'Task ID',
            'oper_id'        => 'Oper ID',
            'close_oper_id'  => 'Close Oper ID',
            'from_step_id'   => 'From Step ID',
            'step_id'        => 'Step ID',
            'rule2_id' => 'Rule ID',
            'start_date'     => 'Start Date',
            'end_date'       => 'End Date',
            'data_json'      => 'Data Json',
            'is_overdue'     => 'Is Overdue',
            'priority_value' => 'priority_value',
            'escalation'     => 'Эскалация',
        ];
    }

    public static function find()
    {
        return new Req3TasksStepHistoryQuery(get_called_class());
    }

    // ============================================================================
    // ============================== ПРАВИЛА =====================================
    // ============================================================================

    public function rules()
    {
        return [
            [['is_overdue'], 'default', 'value' => 0],
            [['escalation'], 'default', 'value' => 0],

            [['from_task_id', 'task_id', 'oper_id', 'close_oper_id', 'from_step_id', 'step_id', 'rule2_id', 'is_overdue', 'escalation'], 'integer'],
            [['task_id', 'oper_id', 'step_id', 'start_date'], 'required'],
            [['priority_value'], 'number'],
            [['start_date', 'end_date'], 'safe'],
            [['data_json'], 'string'],
        ];
    }

    // ============================================================================
    // ============================== СЦЕНАРИИ ====================================


    // ============================================================================
    // ============================== ГЕТТЕРЫ =====================================
    // ============================================================================
    public function getFrom_task()
    {
        return $this->hasOne(Req3Tasks::class, ['id' => 'from_task_id']);
    }

    public function getTask()
    {
        return $this->hasOne(Req3Tasks::class, ['id' => 'task_id']);
    }

    public function getFrom_step()
    {
        return $this->hasOne(Req3TemplateSteps::class, ['id' => 'from_step_id']);
    }

    public function getStep()
    {
        return $this->hasOne(Req3TemplateSteps::class, ['id' => 'step_id']);
    }

    public function getRule2()
    {
        return $this->hasOne(Req3TemplateStepRule2::class, ['id' => 'rule2_id']);
    }

    public function getOper()
    {
        return $this->hasOne(Opers::class, ['oper_id' => 'oper_id']);
    }

    public function getClose_oper()
    {
        return $this->hasOne(Opers::class, ['oper_id' => 'close_oper_id']);
    }

    public function getQueue_label()
    {
        return $this->hasOne(Req3QueueLabels::class, ['id' => 'queue_label_id']);
    }

    // ============================================================================
    // ============================== СЕТТЕРЫ =====================================
    // ============================================================================

    // ============================================================================
    // ============================== СОБЫТИЯ СОХРАНЕНИЯ ==========================
    // ============================================================================

    // ============================================================================
    // ============================== СОБЫТИЯ УДАЛЕНИЯ ============================
    // ============================================================================

    // ============================================================================
    // ============================== ЧТО КАСАЕТСЯ OBJECT =========================
    // ============================================================================
    public function getDataArray()
    {
        $data = [];
        if (!empty($this->data_json)) {
            try {
                $data = json_decode($this->data_json, true);
            } catch (Exception $ignore) {
            }
        }
        if (!is_array($data)) $data = [];
        return $data;
    }

    public function addData($data, $save = true)
    {
        $data = ArrayHelper::merge($this->getDataArray(), $data);
        $this->data_json = json_encode($data);
        if ($save) {
            $this->saveData();
        }
    }

    public function saveData()
    {
        $this->save(false, ['data_json']);
    }

    public function isDeviationJobComplete()
    {
        $log_data = $this->getDataArray();
        if (isset($log_data['deviation_job'])) {
            return true;
        }
        return false;
    }

    // ============================================================================
    // ============================== STATIC ======================================
    // ============================================================================
    /**
     * запускать после updateDataBackSet и ДО initPriority и initDateOverdue
     * @param Req3Tasks $task
     * @param int|null $oper_id
     * @param int|null $from_task_id
     * @param int|null $from_step_id
     * @param int|null $from_rule2_id
     * @param bool      $previous_step_is_overdue
     */
    public static function addStep(Req3Tasks $task, ?int $oper_id = null, ?int $from_task_id = null, ?int $from_step_id = null, ?int $from_rule2_id = null, bool $previous_step_is_overdue = false)
    {
        $old = Req3TasksStepHistory::find()->andWhere(['task_id' => $task->id, 'end_date' => null])->all();
        foreach ($old as $history) {
            if ($history->step_id == $from_step_id) {
                $history->priority_value = $task->priority_value;
            }
            $history->end_date = new Expression("NOW()");
            $history->close_oper_id = $oper_id;
            $history->is_overdue = $previous_step_is_overdue ? 1 : 0;
            $history->queue_label_id = $task->queue_label_id;
            $history->save();
        }

        $history = new Req3TasksStepHistory();
        $history->task_id = $task->id;
        $history->oper_id = $oper_id;
        $history->from_task_id = $from_task_id;
        $history->from_step_id = $from_step_id;
        $history->step_id = $task->step_id;
        $history->rule2_id = $from_rule2_id;
        $history->start_date = new Expression("NOW()");
        $history->escalation = $task->escalation;
        $history->save();

        unset($task->step_history);
    }

}
