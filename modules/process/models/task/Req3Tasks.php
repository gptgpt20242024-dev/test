<?php

namespace app\modules\process\models\task;

use app\components\BaseActiveQuery;
use app\components\CacheTrait;
use app\components\Color;
use app\components\DangerLog;
use app\components\Date;
use app\components\ModelPreload;
use app\components\simpleCalDAV\CalDAVCalendar;
use app\components\simpleCalDAV\SimpleCalDAVClient;
use app\components\Str;
use app\constants\ColorPeriods;
use app\models\FinManagers;
use app\models\OperRoleFmLink;
use app\models\Opers;
use app\models\OpersFms;
use app\modules\address\models\Locations;
use app\modules\counterparties\models\Counterparties;
use app\modules\counterparties\models\CounterpartiesOpers;
use app\modules\crash\models\CrashLinkReq3;
use app\modules\process\components\functions\FunctionCounterpartyPhysUpdate;
use app\modules\process\components\HelperConditions;
use app\modules\process\components\HelperConsole;
use app\modules\process\components\HelperOper;
use app\modules\process\components\HelperTask;
use app\modules\process\dto\RuleDataDto;
use app\modules\process\exceptions\BadStepException;
use app\modules\process\exceptions\LoopException;
use app\modules\process\models\_query\Req3TasksChatsQuery;
use app\modules\process\models\_query\Req3TasksQuery;
use app\modules\process\models\calls\Req3CallsConnectOpers;
use app\modules\process\models\calls\Req3CallsStatuses;
use app\modules\process\models\chats\Req3TasksChats;
use app\modules\process\models\FormCreateReq3;
use app\modules\process\models\FormReq3Search;
use app\modules\process\models\identifiers\Req3IdentifierOrders;
use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\Req3FunctionBase;
use app\modules\process\models\Req3QueueLabels;
use app\modules\process\models\Req3TaskCallLinks;
use app\modules\process\models\task_data\Req3TasksDataComments;
use app\modules\process\models\task_data\Req3TasksDataCommentsFiles;
use app\modules\process\models\task_data\Req3TasksDataItemAddress;
use app\modules\process\models\task_data\Req3TasksDataItemProjectTree;
use app\modules\process\models\task_data\Req3TasksDataItems;
use app\modules\process\models\task_data\Req3TasksDataItemSteps;
use app\modules\process\models\task_data\Req3TasksDataLikes;
use app\modules\process\models\task_data\Req3TasksDataRemarks;
use app\modules\process\models\task_opers\Req3TaskOperOnline;
use app\modules\process\models\task_opers\Req3TaskOperStepCurrent;
use app\modules\process\models\task_opers\Req3TaskOperStepCurrentBosses;
use app\modules\process\models\task_opers\Req3TaskOperStepOther;
use app\modules\process\models\task_opers\Req3TasksObservers;
use app\modules\process\models\task_reports\Req3AddressTasksLead;
use app\modules\process\models\task_reports\Req3TaskReportDeviationLast;
use app\modules\process\models\task_reports\Req3TaskReportHideIdentifier;
use app\modules\process\models\task_reports\Req3TaskReportLead;
use app\modules\process\models\task_reports\Req3TaskReportProblems;
use app\modules\process\models\task_reports\Req3TaskReportQueue;
use app\modules\process\models\template\Req3TemplateLinkTemplate;
use app\modules\process\models\template\Req3TemplateLog;
use app\modules\process\models\template\Req3Templates;
use app\modules\process\models\template\Req3TemplateVersions;
use app\modules\process\models\template_steps\Req3TemplateDataDeviation;
use app\modules\process\models\template_steps\Req3TemplateStepRoles;
use app\modules\process\models\template_steps\Req3TemplateStepRule2;
use app\modules\process\models\template_steps\Req3TemplateStepRule2Functions;
use app\modules\process\models\template_steps\Req3TemplateSteps;
use app\modules\process\models\work_raters\Req3WorkRaterOperConfirmation;
use app\modules\process\services\ProcessTaskService;
use app\modules\scheduler\components\HelperThread;
use app\modules\scheduler\models\Scheduler;
use app\modules\setting\constants\SettingItemType;
use app\modules\setting\constants\SettingLinkType;
use app\modules\setting\dto\SettingDto;
use app\modules\setting\services\SettingService;
use app\modules\utm\models\DiscountPeriods;
use app\modules\utm\models\ServicesData;
use Exception;
use Throwable;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\ServerErrorHttpException;
use yii\web\UploadedFile;

/**
 * This is the model class for table "req3_tasks".
 *
 * @property integer                         $id
 * @property string                          $name
 * @property integer                         $template_id
 * @property integer                         $version_id
 * @property integer                         $step_id
 * @property integer                         $transition_id @deprecated
 * @property integer                         $creator_id
 * @property integer                         $initiator_id
 * @property integer                         $counterparty_customer_id
 * @property integer                         $hired_oper_id
 * @property string                          $create_date
 * @property string                          $date_start_step
 * @property string                          $date_overdue_step
 * @property string                          $date_last_trigger_check
 * @property string                          $date_next_trigger_check
 * @property string                          $date_when_hired
 * @property double                          $priority_value
 * @property integer                         $escalation
 * @property integer                         $queue_label_id
 * @property integer                         $is_testing
 * @property integer                         $is_deleted
 * @property integer                         $deleted_oper_id
 * @property string                          $deleted_date
 * @property integer                         $fm_id
 * @property string                          $function_solves
 *
 * @property Req3Templates                   $template
 * @property Req3TemplateVersions            $version
 * @property Req3TemplateSteps               $step
 *
 * @property FinManagers                     $fm
 * @property Req3TasksComments[]             $comments
 * @property Req3TasksDataItems[]            $data
 * @property Req3TasksDataItems[]            $data_history
 * @property Req3TasksObservers[]            $observers
 * @property Opers                           $creator
 * @property Opers                           $initiator
 * @property Counterparties                  $counterparty_customer
 * @property Opers                           $hired_oper
 * @property Req3TasksStepHistory[]          $step_history
 * @property Req3TaskStartedSubTask          $parent_task
 * @property Req3TaskStartedSubTask[]        $sub_tasks
 * @property Req3TaskOperStepCurrentBosses[] oper_bosses
 * @property Req3TaskOperStepOther[]         oper_see
 * @property Req3TaskOperStepCurrent[]       $opers
 * @property Req3TaskOperStepCurrent[]       $executors
 * @property Req3TaskOperStepCurrent[]       $controllers
 * @property Req3TaskOperStepCurrent[]       $workers
 * @property Req3CallsStatuses[]             $calls_statuses
 * @property Req3CallsStatuses[]             $calls_statuses_step
 * @property Req3TaskReportLead              $lead
 * @property CrashLinkReq3                   $crash_link
 * @property Req3TasksDataRemarks[]          $remarks
 * @property Req3TaskStartedOrders[]         $started_orders
 * @property Req3TasksChats[]                $chats
 * @property Req3TasksChats                  $active_chat
 * @property Req3QueueLabels                 $queue_label
 * @property Req3TaskFromEmail               $started_from_email
 */
class Req3Tasks extends ActiveRecord
{
    use CacheTrait;

    // ============================================================================
    // ============================== КОНСТАНТЫ ===================================
    // ============================================================================
    const COLOR_TIME_AUTO         = Color::COLOR_SECONDARY;
    const COLOR_TIME_ACTIVE       = Color::COLOR_SUCCESS;
    const COLOR_TIME_EXPIRED_1_3  = Color::COLOR_ACTIVE;
    const COLOR_TIME_EXPIRED_4_7  = Color::COLOR_DEFAULT;
    const COLOR_TIME_EXPIRED_8_14 = Color::COLOR_WARNING;
    const COLOR_TIME_EXPIRED_15   = Color::COLOR_DANGER;

    // ============================================================================
    // ============================== ДОПОЛНИТЕЛЬНЫЕ ПОЛЯ =========================
    // ============================================================================
    public  $tag;
    private $cacheClones = [];

    // ============================================================================
    // ============================== ИНИТ ========================================
    // ============================================================================
    public static function tableName()
    {
        return 'req3_tasks';
    }

    public function attributeLabels()
    {
        return [
            'id'                       => 'ID',
            'name'                     => 'Название задачи',
            'template_id'              => 'Шаблон',
            'version_id'               => 'Version ID',
            'step_id'                  => 'Step ID',
            'creator_id'               => 'Creator ID',
            'initiator_id'             => 'Initiator ID',
            'counterparty_customer_id' => 'Заказчик',
            'create_date'              => 'Create Date',
            'date_start_step'          => 'date_start_step',
            'date_overdue_step'        => 'date_overdue_step',
            'date_last_trigger_check'  => 'date_last_trigger_check',
            'date_next_trigger_check'  => 'date_next_trigger_check',
            'priority_value'           => 'Priority Value',
            'escalation'               => 'Эскалация',
            'is_testing'               => 'Тестовая задача',
            'is_deleted'               => 'Is Deleted',
            'deleted_oper_id'          => 'кто удалил',
            'deleted_date'             => 'когда удалили',
            'fm_id'                    => 'Фин менеджер',
            'function_solves'          => 'Решения с функциями',
        ];
    }

    public static function find($only_active = true, $only_production = false)
    {
        $query = new Req3TasksQuery(get_called_class());
        if ($only_active) $query->active();
        if ($only_production) $query->production();
        return $query;
    }

    // ============================================================================
    // ============================== ПРАВИЛА =====================================
    // ============================================================================

    // ============================================================================
    // ============================== ГЕТТЕРЫ =====================================
    // ============================================================================
    public function getTemplate()
    {
        return $this->hasOne(Req3Templates::class, ['id' => 'template_id']);
    }

    public function getVersion()
    {
        return $this->hasOne(Req3TemplateVersions::class, ['id' => 'version_id']);
    }

    public function getStep()
    {
        return $this->hasOne(Req3TemplateSteps::class, ['id' => 'step_id']);
    }

    public function getFm()
    {
        return $this->hasOne(FinManagers::class, ['fm_id' => 'fm_id']);
    }

    public function getComments()
    {
        return $this->hasMany(Req3TasksComments::class, ['task_id' => 'id'])->orderBy(['date_add' => SORT_ASC]);
    }

    public function getData()
    {
        /** @var BaseActiveQuery $query */
        $query = $this->hasMany(Req3TasksDataItems::class, ['link_id' => 'id'])->inverseOf('task');
        $query->andOnCondition(['AND',
            [$query->getMyAlias() . '.parent_id' => null],
            [$query->getMyAlias() . '.is_deleted' => 0],
            [$query->getMyAlias() . '.link_type' => Req3TasksDataItems::LINK_TYPE_TASK]
        ]);
        return $query;
    }

    public function getData_history()
    {
        /** @var BaseActiveQuery $query */
        $query = $this->hasMany(Req3TasksDataItems::class, ['link_id' => 'id'])->inverseOf('task');
        $query->andOnCondition(['AND',
            [$query->getMyAlias() . '.parent_id' => null],
            [$query->getMyAlias() . '.is_deleted' => 1],
            [$query->getMyAlias() . '.link_type' => Req3TasksDataItems::LINK_TYPE_TASK]
        ]);
        return $query;
    }

    public function getObservers()
    {
        return $this->hasMany(Req3TasksObservers::class, ['task_id' => 'id'])->inverseOf('task');
    }

    public function getCreator()
    {
        return $this->hasOne(Opers::class, ['oper_id' => 'creator_id']);
    }

    public function getInitiator()
    {
        return $this->hasOne(Opers::class, ['oper_id' => 'initiator_id']);
    }

    public function getCounterparty_customer()
    {
        return $this->hasOne(Counterparties::class, ['id' => 'counterparty_customer_id']);
    }

    public function getCounterparty_customer_opers()
    {
        return $this->hasOne(CounterpartiesOpers::class, ['counterparty_id' => 'counterparty_customer_id']);
    }

    public function getHired_oper()
    {
        return $this->hasOne(Opers::class, ['oper_id' => 'hired_oper_id']);
    }

    public function getStep_history()
    {
        $query = $this->hasMany(Req3TasksStepHistory::class, ['task_id' => 'id']);
        $query->orderBy([$query->getMyAlias() . '.start_date' => SORT_ASC]);
        return $query;
    }

    public function getParent_task()
    {
        return $this->hasOne(Req3TaskStartedSubTask::class, ['sub_task_id' => 'id']);
    }

    public function getSub_tasks()
    {
        return $this->hasMany(Req3TaskStartedSubTask::class, ['task_id' => 'id']);
    }

    //-------------------------------------------------------------------------------------------------
    public function getOper_see()
    {
        return $this->hasMany(Req3TaskOperStepOther::class, ['task_id' => 'id']);
    }

    public function getOper_bosses()
    {
        return $this->hasMany(Req3TaskOperStepCurrentBosses::class, ['task_id' => 'id']);
    }

    public function getOpers()
    {
        return $this->hasMany(Req3TaskOperStepCurrent::class, ['task_id' => 'id']);
    }

    public function getExecutors()
    {
        $query = $this->getOpers();
        return $query->andOnCondition([$query->getMyAlias() . ".type" => Req3TaskOperStepCurrent::TYPE_EXECUTOR]);
    }

    public function getControllers()
    {
        $query = $this->getOpers();
        return $query->andOnCondition([$query->getMyAlias() . ".type" => Req3TaskOperStepCurrent::TYPE_CONTROLLER]);
    }

    public function getWorkers()
    {
        $query = $this->getOpers();
        return $query->andOnCondition([$query->getMyAlias() . ".type" => Req3TaskOperStepCurrent::TYPE_WORKER]);
    }

    //-------------------------------------------------------------------------------------------------

    public function getCalls_statuses()
    {
        return $this->hasMany(Req3CallsStatuses::class, ['task_id' => 'id']);
    }

    public function getCalls_statuses_step()
    {
        return $this->hasMany(Req3CallsStatuses::class, ['task_id' => 'id', 'step_id' => 'step_id']);
    }

    public function getLead()
    {
        return $this->hasOne(Req3TaskReportLead::class, ['task_id' => 'id']);
    }

    public function getCrash_link()
    {
        return $this->hasOne(CrashLinkReq3::class, ['req3_id' => 'id']);
    }

    public function getRemarks()
    {
        return $this->hasMany(Req3TasksDataRemarks::class, ['task_id' => 'id']);
    }

    public function getStarted_orders()
    {
        return $this->hasMany(Req3TaskStartedOrders::class, ['task_id' => 'id']);
    }

    public function getChats()
    {
        return $this->hasMany(Req3TasksChats::class, ['task_id' => 'id']);
    }

    public function getActive_chat()
    {
        /** @var Req3TasksChatsQuery $query */
        $query = $this->hasOne(Req3TasksChats::class, ['task_id' => 'id']);
        return $query->andOnCondition([$query->getMyAlias() . ".is_active" => 1]);
    }

    public function getQueue_label()
    {
        return $this->hasOne(Req3QueueLabels::class, ['id' => 'queue_label_id']);
    }

    public function getStarted_from_email()
    {
        return $this->hasOne(Req3TaskFromEmail::class, ['task_id' => 'id']);
    }

    // ============================================================================
    // ============================== СЕТТЕРЫ =====================================
    // ============================================================================

    // ============================================================================
    // ============================== СОБЫТИЯ СОХРАНЕНИЯ ==========================
    // ============================================================================
    public function save($runValidation = true, $attributeNames = null, $template_default_observers = null)
    {
        $insert = $this->isNewRecord;
        $save = parent::save($runValidation, $attributeNames);

        if ($insert) {
            //Yii::beginProfile("save bp Observers");
            $this->initDefaultObservers($template_default_observers);
            //Yii::endProfile("save bp Observers");
        }

        return $save;
    }

    // ============================================================================
    // ============================== СОБЫТИЯ УДАЛЕНИЯ ============================
    // ============================================================================
    public function beforeDelete()
    {
        Req3TasksStepHistory::deleteAll(['task_id' => $this->id]);
        Req3TasksComments::deleteAll(['task_id' => $this->id]);
        Req3TasksCommentsFiles::deleteAll(['task_id' => $this->id]);
        Req3TasksDataItems::deleteByTaskId($this->id);
        Req3TasksDataComments::deleteAll(['task_id' => $this->id]);
        Req3TasksDataCommentsFiles::deleteAll(['task_id' => $this->id]);
        Req3TasksNotifications::deleteAll(['task_id' => $this->id]);
        Req3TasksObservers::deleteAll(['task_id' => $this->id]);
        Req3TasksTelegramSend::deleteAll(['task_id' => $this->id]);
        Req3TaskCallLinks::deleteAll(['task_id' => $this->id]);
        Req3TaskFromEmail::deleteAll(['task_id' => $this->id]);
        Req3TaskIdentifierClone::deleteAll(['task_id' => $this->id]);
        Req3TaskMigrate::deleteAll(['task_id' => $this->id]);
        Req3TaskOperOnline::deleteAll(['task_id' => $this->id]);
        Req3TaskOperStepOther::deleteAll(['task_id' => $this->id]);
        Req3TaskOperStepCurrentBosses::deleteAll(['task_id' => $this->id]);
        Req3TaskOperStepCurrent::deleteAll(['task_id' => $this->id]);
        Req3TaskReportDeviationLast::deleteAll(['task_id' => $this->id]);
        Req3TaskReportHideIdentifier::deleteAll(['task_id' => $this->id]);
        Req3TaskReportProblems::deleteAll(['task_id' => $this->id]);
        Req3TaskRewardWork::deleteAll(['task_id' => $this->id]);
        Req3TaskStartedFunctions::deleteAll(['task_id' => $this->id]);
        Req3TaskStartedOrders::deleteAll(['task_id' => $this->id]);
        Req3TaskStartedSubTask::deleteAll(['task_id' => $this->id]);
        Req3TasksDataRemarks::deleteAll(['task_id' => $this->id]);
        CrashLinkReq3::deleteAll(['req3_id' => $this->id]);
        Req3TasksChats::deleteAllByTaskId($this->id);
        Req3CallsStatuses::deleteAllByTaskId($this->id);
        Req3TaskReportLead::deleteAllByTaskId($this->id);
        Req3TaskReward::deleteAllByTaskId($this->id);
        Req3TasksDataLikes::deleteAll(['task_id' => $this->id]);

        return parent::beforeDelete(); // TODO: Change the autogenerated stub
    }

    // ============================================================================
    // ============================== ЧТО КАСАЕТСЯ OBJECT =========================
    // ============================================================================
    public function clearData($identifier_ids, $add_history = false, $oper_id = HelperOper::CREATOR_SYSTEM)
    {
        if (!is_array($identifier_ids)) $identifier_ids = [$identifier_ids];

        $task_data = $this->data;

        $count = 0;
        $log = [];
        foreach ($task_data as $key => $item) {
            if (in_array($item->identifier_id, $identifier_ids)) {
                $log[$item->identifier_id] = $item->identifier_id;

                $item->is_deleted = 1;
                $item->save(true, ['is_deleted']);
                unset($task_data[$key]);
                $count++;
            }
        }

        if ($add_history) {
            if (count($log) > 0) {
                $identifiers = Req3Identifiers::find()->id(array_keys($log))->indexBy('id')->all();
                foreach ($log as $identifier_id) {
                    $this->addToHistoryChangeData($identifiers[$identifier_id]->name ?? $identifier_id, [], $oper_id);
                }
            }
        }

        $this->onDataChange($task_data);

        Req3TaskIdentifierClone::deleteByIdentifier($this->id, $identifier_ids);
        return $count;
    }

    public function setData($raw_data, $oper_id = HelperOper::CREATOR_SYSTEM, &$errors = [])
    {
        $names = [];
        foreach ($raw_data as $key => $data) {
            if (is_array($data) && isset($data['identifier_name'])) {
                $names[] = $data['identifier_name'];
            }
        }
        if (count($names) > 0) {
            $identifiers = Req3Identifiers::find()->versionId($this->version_id, true)->identifier($names)->indexBy('identifier')->all();
            foreach ($raw_data as $key => $data) {
                if (is_array($data) && !isset($data['identifier_id']) || !isset($data['type'])) {
                    if (isset($data['identifier_name'])) {
                        $identifier_name = $data['identifier_name'];
                        if (isset($identifiers[$identifier_name])) {
                            $raw_data[$key]['identifier_id'] = $identifiers[$identifier_name]->id;
                            $raw_data[$key]['type'] = $identifiers[$identifier_name]->type;
                        }
                    }
                }
            }
        }

        $data = Req3TasksDataItems::loadCreateObject($raw_data, $this, $oper_id, $errors);

        //переносим в историю данные этих идентификаторов
        foreach ($data as $value) {
            if ($value->link_id != $this->id || $value->link_type != Req3TasksDataItems::LINK_TYPE_TASK) {
                $value->setLink($this);
            }
            $this->clearData($value->identifier_id);
        }

        $log = [];

        $task_data = $this->data;
        foreach ($data as $value) {
            $value->markAttributeDirty('is_deleted');
            $value->is_deleted = 0;
            if (!$value->save(true, null, true)) {
                throw new ServerErrorHttpException("Ошибка сохранения данных: " . implode(", ", $value->getFirstErrors()));
            }
            $task_data[] = $value;

            $simple_value = $value->getSimpleDataValues();
            $log[$value->identifier_id][] = $simple_value['value'];
        }

        if (count($log) > 0) {
            $identifiers = Req3Identifiers::find()->id(array_keys($log))->indexBy('id')->all();
            foreach ($log as $identifier_id => $values) {
                $this->addToHistoryChangeData($identifiers[$identifier_id]->name ?? $identifier_id, $values, $oper_id);
            }
        }

        $this->onDataChange($task_data);
    }


    /**
     * @param      $type
     * @param bool $multi
     * @return Req3TasksDataItems[]|Req3TasksDataItems
     */
    public function getDataIdentifierByType($type, $multi = true)
    {
        $items = [];
        foreach ($this->data as $item) {
            if ($item->type == $type) {
                if (!$multi) return $item;
                $items[] = $item;
            }
        }

        if ($multi) return $items;
        else return null;
    }

    /**
     * @param      $identifier_id
     * @param bool $multi
     * @param bool $and_sub
     * @return Req3TasksDataItems[]|Req3TasksDataItems
     */
    public function getDataIdentifier($identifier_id, $multi = true, $and_sub = false, $identifier_id_is_identifier_name = false)
    {
        $fnct_get_items = function ($data) use ($identifier_id, $multi, $and_sub, &$fnct_get_items, $identifier_id_is_identifier_name) {
            /** @var Req3TasksDataItems[] $data */
            $items = [];
            foreach ($data as $item) {


                if (!$identifier_id_is_identifier_name) {
                    if ($item->identifier_id == $identifier_id) {
                        if (!$multi) return $item;
                        $items[] = $item;
                    }
                } else {
                    if (($item->identifier->identifier ?? null) == $identifier_id) {
                        if (!$multi) return $item;
                        $items[] = $item;
                    }
                }

                if ($and_sub && $item->type == Req3Identifiers::TYPE_GROUP) {
                    $find_in_sub = $fnct_get_items($item->children);
                    if (!$multi) {
                        if ($find_in_sub) {
                            return $find_in_sub;
                        }
                    } else {
                        foreach ($find_in_sub as $sub_item) {
                            $items[] = $sub_item;
                        }
                    }
                }
            }
            if ($multi) return $items;
            else return null;
        };

        if ($identifier_id_is_identifier_name) {
            ModelPreload::preload($this->data, ['identifier']);
        }

        return $fnct_get_items($this->data);
    }

}