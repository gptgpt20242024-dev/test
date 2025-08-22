<?php

namespace app\modules\process\models\task_data;

use app\models\Opers;
use app\modules\process\models\_query;
use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\work_raters\Req3WorkRaterOperConfirmation;
use app\modules\process\models\work_raters\Req3WorkRaterOperQualification;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "req3_tasks_data_remarks".
 *
 * @property integer         $id
 * @property integer         $task_id
 * @property integer         $identifier_id
 * @property integer         $oper_send_id
 * @property string          $date_send
 * @property string          $comment
 * @property integer         $is_problem_work
 * @property integer         $is_problem_executor
 * @property integer         $executor_id
 * @property integer         $is_problem_task
 * @property integer         $is_approved
 * @property string          $date_approved
 * @property integer         $oper_approved_id
 *
 * @property Opers           $oper_send
 * @property Opers           $oper_approved
 * @property Opers           $oper_executor
 * @property Req3Tasks       $task
 * @property Req3Identifiers $identifier
 */
class Req3TasksDataRemarks extends ActiveRecord
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
        return 'req3_tasks_data_remarks';
    }


    public function attributeLabels()
    {
        return [
            'id'                  => 'ID',
            'task_id'             => 'Task ID',
            'identifier_id'       => 'Identifier ID',
            'oper_send_id'        => 'Oper Send ID',
            'date_send'           => 'Date Send',
            'comment'             => 'Что исправить ?',
            'is_problem_work'     => 'Проблема в работе',
            'is_problem_executor' => 'Проблема в исполнителе',
            'executor_id'         => 'Исполнитель',
            'is_problem_task'     => 'Проблема в задаче',
            'is_approved'         => 'Is Approved',
            'date_approved'       => 'Date Approved',
            'oper_approved_id'    => 'Oper Approved ID',
        ];
    }


    public static function find()
    {
        return new _query\Req3TasksDataRemarksQuery(get_called_class());
    }

    // ============================================================================
    // ============================== ПРАВИЛА =====================================
    // ============================================================================
    public function rules()
    {
        return [
            ['executor_id', 'filter', 'filter' => function ($value) {
                return $this->is_problem_executor ? $value : null;
            }],

            [['task_id', 'identifier_id', 'oper_send_id', 'date_send', 'comment'], 'required'],
            [['task_id', 'identifier_id', 'oper_send_id', 'is_problem_work', 'is_problem_executor', 'is_problem_task', 'is_approved', 'oper_approved_id'], 'integer'],
            [['date_send', 'date_approved'], 'safe'],
            [['comment'], 'string'],
        ];
    }

    // ============================================================================
    // ============================== СЦЕНАРИИ ====================================
    // ============================================================================

    // ============================================================================
    // ============================== ГЕТТЕРЫ =====================================
    // ============================================================================
    public function getOper_send()
    {
        return $this->hasOne(Opers::class, ['oper_id' => 'oper_send_id']);
    }

    public function getOper_approved()
    {
        return $this->hasOne(Opers::class, ['oper_id' => 'oper_approved_id']);
    }

    public function getOper_executor()
    {
        return $this->hasOne(Opers::class, ['oper_id' => 'executor_id']);
    }

    public function getTask()
    {
        return $this->hasOne(Req3Tasks::class, ['id' => 'task_id']);
    }

    public function getIdentifier()
    {
        return $this->hasOne(Req3Identifiers::class, ['id' => 'identifier_id']);
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
    /**
     * @param           $identification
     * @param Req3Tasks $task
     * @return bool
     */
    public function canAccept($identification, $task = null)
    {
        if (!$this->is_approved) {
            $oper = Opers::getOperByData($identification);
            $task = $task ?? $this->task;
            if ($task && $task->id == $this->task_id) {
                return $this->executor_id == $oper->oper_id;
            }
        }
        return false;
    }

    /**
     * @param Req3Tasks       $task
     * @param Req3Identifiers $identifier
     * @return bool
     */
    public function canAcceptWithoutNewData($task = null, $identifier = null)
    {
        $identifier = $identifier ?? $this->identifier;
        $task = $task ?? $this->task;
        if ($task && $identifier) {
            $is_custom_editable = $identifier->isCustomEdit();
            if ($is_custom_editable) return true;

            $data = $task->getDataIdentifier($this->identifier_id, false);
            $need_new_data = $data != null && $data->oper_id == $this->executor_id && $identifier->isEditable($task);

            return !$need_new_data;
        }
        return false;
    }

    public function isExecutorReReadWorkRaterDocuments()
    {
        if ($this->identifier && !empty($this->identifier->work_rated_id) && $this->identifier->work_rated) {
            $qualification = Req3WorkRaterOperQualification::findOrCreate($this->executor_id, $this->identifier->work_rated_id);
            if ($qualification->status != Req3WorkRaterOperQualification::STATUS_VERIFIED) {
                $confirmation = Req3WorkRaterOperConfirmation::findOrCreateByTask($this->executor_id, $this->identifier->work_rated_id, $this->task_id, $this->identifier_id);
                if ($confirmation->date_open_standard == null || strtotime($confirmation->date_open_standard) < strtotime($this->date_send)) {
                    return false;
                }
                if ($confirmation->date_open_regulations == null || strtotime($confirmation->date_open_regulations) < strtotime($this->date_send)) {
                    return false;
                }
            }
        }
        return true;
    }




    // ============================================================================
    // ============================== STATIC ======================================
    // ============================================================================
}
