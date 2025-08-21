<?php

namespace app\modules\process\models\template_steps;

use app\components\CacheTrait;
use app\components\Date;
use app\models\FinManagers;
use app\models\OperRoleFmLink;
use app\models\Opers;
use app\models\OpersFms;
use app\modules\process\models\_query\Req3ProcessStepRolesQuery;
use app\modules\process\models\_query\Req3TemplateStepsQuery;
use app\modules\process\models\calls\Req3CallsConnectOpers;
use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\Req3QueueLabels;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task\Req3TasksStepHistory;
use app\modules\process\models\task\Req3UpdateRole;
use app\modules\process\models\template\Req3TemplateLog;
use app\modules\process\models\template\Req3Templates;
use app\modules\process\models\template\Req3TemplateVersions;
use app\modules\process\models\template\Req3TemplateVersionTagLink;
use app\modules\process\models\work_raters\Req3WorkRaters;
use Exception;
use Throwable;
use Yii;
use yii\db\ActiveRecord;
use yii\web\BadRequestHttpException;

/**
 * This is the model class for table "req3_template_steps".
 *
 * @property integer                      $id
 * @property integer                      $template_id
 * @property integer                      $version_id
 * @property integer                      $group_id
 * @property string                       $name
 * @property integer                      $is_first
 * @property integer                      $is_last
 * @property integer                      $last_status
 * @property integer                      $is_deviation
 * @property integer                      $is_deviation_architect
 * @property integer                      $is_auto
 * @property integer                      $is_calls
 * @property integer                      $calls_from_id
 * @property integer                      $calls_setting_user_admin_block
 * @property integer                      $execute_minutes
 * @property integer                      $execute_only_work_time
 * @property integer                      $execute_from_type
 * @property integer                      $execute_from_id
 * @property integer                      $real_minutes
 * @property integer                      $is_executors_parent
 * @property integer                      $is_controllers_parent
 * @property integer                      $address_from_id
 * @property integer                      $_priority_value
 * @property string                       $priority_custom
 * @property string                       $priority_info
 * @property integer                      $is_sync_calendar
 * @property integer                      $is_notify_when_leaving
 * @property integer                      $queue_label_from_type
 * @property integer                      $queue_label_from_id
 * @property integer                      $queue_label_from_id2
 * @property integer                      $work_rated_id
 * @property string                       $info_indicators
 * @property string                       $info_control
 * @property integer                      $sort_order
 * @property integer                      $block_by_oper_id
 * @property integer                      $force_auto_recheck_interval
 * @property integer                      $delete_after_days
 *
 * @property Req3Templates                $template
 * @property Req3TemplateVersions         $version
 * @property Req3TemplateStepRoles[]      $all_roles
 * @property Req3TemplateStepRoles[]      $controllers
 * @property Req3TemplateStepRoles[]      $executors
 * @property Req3TemplateStepRoles[]      $workers
 * @property Req3TemplateStepRoles[]      $accepted_create
 * @property Req3TemplateStepView[]       $views
 * @property Req3CallsConnectOpers[]      $calls_connect_opers
 * @property Req3Tasks[]                  $tasks
 * @property Req3Identifiers              $execute_from
 * @property Req3Identifiers              $address_from
 * @property Req3Identifiers              $calls_from
 * @property Req3TemplateVersionTagLink[] $tag_links
 * @property Req3TemplateStepRule2[]      $rules
 * @property Req3WorkRaters               $work_rated
 * @property Req3Identifiers              $queue_label_identifier
 * @property Req3Identifiers              $queue_label_identifier2
 * @property Req3QueueLabels              $queue_label
 * @property Opers                        $blockOper
 *
 */
class Req3TemplateSteps extends ActiveRecord
{
    use CacheTrait;

    // ============================================================================
    // ============================== КОНСТАНТЫ ===================================
    // ============================================================================
    const EXECUTE_FROM_START_STEP = 1;
    const EXECUTE_FROM_START_TASK = 2;
    const EXECUTE_FROM_IDENTIFIER = 3;
    const EXECUTE_FROM_NAMES      = [
        self::EXECUTE_FROM_START_STEP => "От старта шага",
        self::EXECUTE_FROM_START_TASK => "От старта задачи",
        self::EXECUTE_FROM_IDENTIFIER => "От идентификатора",
    ];

    const LAST_STATUS_SUCCESS = 1;
    const LAST_STATUS_FAILED  = 2;
    const LAST_STATUS_NAMES   = [
        self::LAST_STATUS_SUCCESS => "Успех",
        self::LAST_STATUS_FAILED  => "Провал",
    ];

    const QUEUE_LABEL_FROM_STATIC     = 1;
    const QUEUE_LABEL_FROM_IDENTIFIER = 2;
    const QUEUE_LABEL_FROM_NAMES      = [
        self::QUEUE_LABEL_FROM_STATIC     => "Статическая",
        self::QUEUE_LABEL_FROM_IDENTIFIER => "Из идентификатора",
    ];

    const QUEUE_LABEL_IDENTIFIER_ALLOWED = [
        Req3Identifiers::TYPE_QUEUE_LABEL,
        Req3Identifiers::TYPE_LIST,
        Req3Identifiers::TYPE_LIST_TREE,
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
        return 'req3_template_steps';
    }

    public function attributeLabels()
    {
        return [
            'id'                             => 'ID',
            'template_id'                    => 'template_id',
            'version_id'                     => 'Version ID',
            'group_id'                       => 'group_id',
            'name'                           => 'Название',
            'is_first'                       => 'Первый шаг',
            'is_last'                        => 'Последний шаг',
            'last_status'                    => 'Статус последнего шага',
            'is_deviation'                   => 'Шаг отклонения',
            'is_deviation_architect'         => 'Шаг отклонения для архитектора',
            'is_auto'                        => 'Автоматический шаг',
            'is_calls'                       => 'Шаг для обзвонки',
            'calls_from_id'                  => 'Данные для обзвонки',
            'calls_setting_user_admin_block' => 'Проверять что стоит админская блокировка',
            'execute_minutes'                => 'Время выполнения (минут)',
            'execute_only_work_time'         => 'Считать только по рабочим часам',
            'execute_from_type'              => 'Считать от:',
            'execute_from_id'                => 'Идентификатор',
            'real_minutes'                   => 'Трудозатраты (минут)',
            'is_executors_parent'            => 'Использовать начальников выбранных ролей',
            'is_controllers_parent'          => 'Использовать начальников выбранных ролей',
            'address_from_id'                => 'Идентификатор для адреса',
            'priority_custom'                => 'Priority Custom',
            'priority_info'                  => 'Информация о приоритете',
            'is_sync_calendar'               => 'Синхронизировать в календарь',
            'is_notify_when_leaving'         => 'Уведомлять исполнителей текущего шага о покидании шага',
            'queue_label_from_type'          => 'Откуда брать данные метки очереди',
            'queue_label_from_id'            => 'Данные метки очереди',
            'queue_label_from_id2'           => 'Данные метки очереди (2)',
            'work_rated_id'                  => 'Работа',
            'info_indicators'                => 'Показатели',
            'info_control'                   => 'Контроль',
            'sort_order'                     => 'sort_order',
            'block_by_oper_id'               => 'block_by_oper_id',
            'force_auto_recheck_interval'    => 'Время принудительной перепроверки автоматики (минут)',
            'delete_after_days'              => 'Удалять по прошествию периода (дней)',
        ];
    }


}
