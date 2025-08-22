<?php

namespace app\modules\process\models\task_data;

use app\models\ActiveRecordCache;
use app\models\Opers;
use app\modules\process\models\_query\Req3TasksDataItemIdentifierCommentsQuery;
use app\modules\process\models\_query\Req3TasksDataItemsQuery;
use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\Req3Setting;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\template\Req3Templates;
use app\modules\process\models\template_steps\Req3TemplateSteps;
use Yii;

/**
 * This is the model class for table "req3_tasks_data_item_identifier_comments".
 *
 * @property integer $id
 * @property integer $identifier_id
 * @property integer $step_id
 * @property integer $work_rated_id
 * @property integer $item_id
 * @property integer $task_id
 * @property integer $oper_id
 * @property string  $comment
 * @property integer $status_execution
 * @property integer $status_check
 * @property string  $date_add
 *
 * @property Req3Tasks                                 $task
 * @property Opers                                     $oper
 * @property Req3TasksDataItems                        $item
 * @property Req3Identifiers                           $identifier
 * @property Req3TemplateSteps                         $step
 * @property Req3TasksDataItemIdentifierCommentLikes[] $likes
 */
class Req3TasksDataItemIdentifierComments extends ActiveRecordCache
{
    // ============================================================================
    // ============================== КОНСТАНТЫ ===================================
    // ============================================================================
    //статусы выполнения
    const STATUS_EXECUTION_PENDING_VERIFICATION = 1;
    const STATUS_EXECUTION_COMPLETED            = 2;
    const STATUS_EXECUTION_IN_WORK              = 3;
    const STATUS_EXECUTION_CANCELED             = 4;
    const STATUS_EXECUTION_NAMES                = [
        self::STATUS_EXECUTION_PENDING_VERIFICATION => "Ожидает проверку",
        self::STATUS_EXECUTION_COMPLETED            => "Выполнено",
        self::STATUS_EXECUTION_IN_WORK              => "Взял в работу",
        self::STATUS_EXECUTION_CANCELED             => "Отменил",
    ];

    //статусы проверки выполнения
    const STATUS_CHECK_WAIT     = 1;
    const STATUS_CHECK_APPROVE  = 2;
    const STATUS_CHECK_CANCELED = 3;
    const STATUS_CHECK_NAMES    = [
        self::STATUS_CHECK_WAIT     => "Ожидает проверку",
        self::STATUS_CHECK_APPROVE  => "Принимаю",
        self::STATUS_CHECK_CANCELED => "Отклонил",
    ];

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
        return 'req3_tasks_data_item_identifier_comments';
    }


    public function attributeLabels()
    {
        return [
            'id'               => 'ID',
            'identifier_id'    => 'Identifier ID',
            'step_id'          => 'Step ID',
            'work_rated_id'    => 'Work Rated ID',
            'item_id'          => 'Item ID',
            'task_id'          => 'Task ID',
            'oper_id'          => 'Oper ID',
            'comment'          => 'Comment',
            'status_execution' => 'Status Execution',
            'status_check'     => 'Status Check',
            'date_add'         => 'Date Add',
        ];
    }


    public static function find()
    {
        return new Req3TasksDataItemIdentifierCommentsQuery(get_called_class());
    }

    // ============================================================================
    // ============================== ПРАВИЛА =====================================
    // ============================================================================
    public function rules()
    {
        return [
            [['identifier_id', 'step_id', 'work_rated_id', 'item_id', 'task_id', 'oper_id', 'status_execution', 'status_check'], 'integer'],
            [['item_id', 'oper_id', 'comment', 'status_execution', 'status_check', 'date_add'], 'required'],
            [['comment'], 'string'],
            [['date_add'], 'safe'],
        ];
    }

    // ============================================================================
    // ============================== СЦЕНАРИИ ====================================
    // ============================================================================

    // ============================================================================
    // ============================== ГЕТТЕРЫ =====================================
    // ============================================================================
    public function getTask()
    {
        return $this->hasOne(Req3Tasks::class, ['id' => 'task_id']);
    }

    public function getOper()
    {
        return $this->hasOne(Opers::class, ['oper_id' => 'oper_id']);
    }

    public function getItem()
    {
        return $this->hasOne(Req3TasksDataItems::class, ['id' => 'item_id']);
    }

    public function getIdentifier()
    {
        return $this->hasOne(Req3Identifiers::class, ['id' => 'identifier_id']);
    }

    public function getStep()
    {
        return $this->hasOne(Req3TemplateSteps::class, ['id' => 'step_id']);
    }

    public function getLikes()
    {
        return $this->hasMany(Req3TasksDataItemIdentifierCommentLikes::class, ['comment_id' => 'id']);
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
    public function beforeDelete()
    {
        Req3TasksDataItemIdentifierCommentLikes::deleteAll(['comment_id' => $this->id]);

        return parent::beforeDelete();
    }

    public static function deleteAllByDataIds(array $ids)
    {
        $commentIds = self::find()->where(['item_id' => $ids])->select('id')->column();
        if (!empty($commentIds)) {
            Req3TasksDataItemIdentifierCommentLikes::deleteAll(['comment_id' => $commentIds]);
            self::deleteAll(['id' => $commentIds]);
        }
    }

    // ============================================================================
    // ============================== ЧТО КАСАЕТСЯ OBJECT =========================
    // ============================================================================
    public function getResponsibleRole()
    {
        if (!empty($this->task_id) && !empty($this->task->template->responsible_role ?? null)) {
            return $this->task->template->responsible_role;
        }
        if (!empty($this->step_id) && !empty($this->step->template->responsible_role ?? null)) {
            return $this->step->template->responsible_role;
        }
        if (!empty($this->identifier_id) && $this->identifier && !empty($this->identifier->template_id) && !empty($this->identifier->template->responsible_role ?? null)) {
            return $this->identifier->template->responsible_role;
        }
        return null;
    }

    public function isAccessSetStatusExecution($oper_id)
    {
        $access = Yii::$app->authManager->checkAccess($oper_id, "business.work_rater.confirmation_of_work_changes");
        if ($access) return true;

        $role = $this->getResponsibleRole();
        if (!$role) return false;

        $roles = Yii::$app->authManager->getRolesByOperId($oper_id);
        return isset($roles[$role]);
    }

    public function isAccessSetStatusCheck($oper_id)
    {
        return $this->oper_id == $oper_id;
    }

    public function getStatusExecutionName()
    {
        $names = self::STATUS_EXECUTION_NAMES;
        return isset($names[$this->status_execution]) ? $names[$this->status_execution] : ("Неизвестный статус {$this->status_execution}");
    }

    public function getStatusCheckName()
    {
        $names = self::STATUS_CHECK_NAMES;
        return isset($names[$this->status_check]) ? $names[$this->status_check] : ("Неизвестный статус {$this->status_check}");
    }

    // ============================================================================
    // ============================== STATIC ======================================
    // ============================================================================
    public static function getIdentifiersAndWorksCheckWork()
    {
        return self::getStatic(function () {
            $data = [
                'identifier_ids' => [],
                'work_rated_ids' => [],
                'step_ids'       => [],
            ];

            $setting = Req3Setting::get(Req3Setting::KEY_CHECK_IDENTIFIER);
            $config = [
                'template_id'      => $setting->value_array['template_id'] ?? null,
                'identifier_check' => $setting->value_array['identifier_identifier'] ?? null,
            ];

            if ($config['template_id'] && $config['identifier_check']) {
                $template = Req3Templates::find()->id($config['template_id'])->one();
                if ($template && $template->active_version) {
                    $identifier = Req3Identifiers::find()->versionId($template->active_version->id)->identifier($config['identifier_check'])->one();
                    if ($identifier) {

                        $tasks = Req3Tasks::find()
                            ->templateId($config['template_id'])
                            ->innerJoinWith(['data' => function ($query) use ($identifier) {
                                /** @var $query Req3TasksDataItemsQuery */
                                $query->type($identifier->type, true);
                                $query->andOnCondition([$query->getMyAlias() . '.identifier_id' => $identifier->id]);
                            }], true)
                            ->notLastStep()
                            ->all();
                        foreach ($tasks as $task) {
                            foreach ($task->data as $item) {
                                if ($item->identifier_id == $identifier->id) {
                                    if ($item->value_number == Req3TasksDataItems::VALUE_NUMBER_TYPE_CHECK_WORK) {
                                        $data['work_rated_ids'][$item->value_id] = $task->id;
                                    }
                                    if ($item->value_number == Req3TasksDataItems::VALUE_NUMBER_TYPE_CHECK_IDENTIFIER) {
                                        $data['identifier_ids'][$item->value_id] = $task->id;
                                    }
                                    if ($item->value_number == Req3TasksDataItems::VALUE_NUMBER_TYPE_CHECK_STEP) {
                                        $data['step_ids'][$item->value_id] = $task->id;
                                    }

                                }
                            }

                        }
                    }
                }
            }
            return $data;
        });
    }
}