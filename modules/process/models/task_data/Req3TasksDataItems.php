<?php

namespace app\modules\process\models\task_data;

use app\components\Date;
use app\components\ModelHelper;
use app\components\Str;
use app\models\FinManagers;
use app\models\OperRoleFmLink;
use app\models\Opers;
use app\models\Phones;
use app\modules\acs\constants\IntercomType;
use app\modules\acs\enrichers\AcsHouseEnricher;
use app\modules\acs\services\AcsService;
use app\modules\address\components\Address;
use app\modules\address\constants\AddressAddData;
use app\modules\address\models\Locations;
use app\modules\address\models\MapAddresses;
use app\modules\address\models\MapAddressesDopData;
use app\modules\address\models\MapHouses;
use app\modules\address\models\MapStreets;
use app\modules\address\services\AddressService;
use app\modules\communication\constants\CommunicationTypes;
use app\modules\communication\dto\ChannelLinkedDto;
use app\modules\communication\ModuleCommunication;
use app\modules\counterparties\models\Counterparties;
use app\modules\counterparties\models\CounterpartiesFiles;
use app\modules\counterparties\models\CounterpartiesFileSetting;
use app\modules\counterparties\models\CounterpartiesFileTypes;
use app\modules\counterparties\models\DocPassportRf;
use app\modules\counterparties\models\DocResidence;
use app\modules\counterparties\models\DocRwp;
use app\modules\counterparties\models\FormAddCounterparty;
use app\modules\crash\models\Reasons;
use app\modules\crash\models\Request;
use app\modules\ktv\services\KtvService;
use app\modules\lists\models\ListsItems;
use app\modules\lists\models\ListsTreeGroupsFindReq3;
use app\modules\lists\models\ListsTreeItems;
use app\modules\oktell\models\CalledPhones;
use app\modules\order\models\Task;
use app\modules\order\models\Template;
use app\modules\order\services\OrderService;
use app\modules\process\models\_query;
use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\Req3QueueLabels;
use app\modules\process\models\Req3Scheduler;
use app\modules\process\models\rewards\Req3RewardItems;
use app\modules\process\models\rewards\Req3RewardServices;
use app\modules\process\models\task\Req3TaskRewardWork;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\template\Req3Templates;
use app\modules\process\models\work_raters\Req3WorkRaterOperConfirmation;
use app\modules\process\models\work_raters\Req3WorkRaterOperQualification;
use app\modules\process\models\work_raters\Req3WorkRaters;
use app\modules\rbac\models\RbacItem;
use app\modules\rbac\models\ValuableFinalProducts;
use app\modules\setting\constants\SettingLinkType;
use app\modules\setting\constants\SettingStatusZone;
use app\modules\setting\dto\SettingDto;
use app\modules\setting\models\SettingLabels;
use app\modules\setting\services\SettingService;
use app\modules\user\constants\UserConnectionStatusTypes;
use app\modules\user\constants\UserCreditTypes;
use app\modules\user\models\Users;
use app\modules\user\models\UsersAdmBlockReasons;
use app\modules\user\models\UsersConnectKnownFrom;
use app\modules\userside\components\UserSideHelper;
use app\modules\userside\services\DeviceService;
use app\modules\utm\models\DiscountPeriods;
use app\modules\utm\models\ServicesData;
use app\modules\utm\models\Tariffs;
use app\modules\warehouse\models\Balance;
use app\modules\warehouse\models\Cap;
use app\modules\warehouse\models\Item;
use app\modules\warehouse\models\ItemSyn;
use app\modules\warehouse\models\Warehouse;
use DateTime;
use Exception;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\UploadedFile;

/**
 * This is the model class for table "req3_tasks_data_items".
 *
 * @property integer                               $id
 * @property integer                               $parent_id
 * @property integer                               $link_id
 * @property integer                               $link_type
 * @property integer                               $identifier_id
 * @property integer                               $type
 * @property integer                               $value_id
 * @property string                                $value_text
 * @property string                                $value_text_idx
 * @property double                                $value_number
 * @property integer                               $oper_id
 * @property integer                               $is_deleted
 * @property integer                               $step_back_set
 * @property string                                $date_add
 *
 * @property Req3Tasks                             $task
 * @property Req3Scheduler                         $scheduler
 * @property Req3Identifiers                       $identifier
 * @property Req3TasksDataItems[]                  $children
 * @property Opers                                 $oper_add
 *
 * @property Users                                 $user
 * @property UsersConnectKnownFrom                 $known_from
 * @property MapAddresses                          $address
 * @property Req3TasksDataItemAddress              $address_link
 * @property FinManagers                           $fm
 * @property Tariffs                               $tariff
 * @property ServicesData                          $service
 * @property DiscountPeriods                       $dp
 * @property Phones                                $phone
 * @property Item                                  $item
 * @property ItemSyn                               $syn_item
 * @property Warehouse                             $warehouse
 * @property Template                              $wh_template
 * @property Opers                                 $oper
 * @property Req3TasksDataFiles                    $file
 * @property ListsItems                            $list_item
 * @property ListsTreeItems                        $list_tree_item
 * @property Counterparties                        $counterparty
 * @property Req3TasksDataItems[]                  $history
 * @property CalledPhones                          $call_name
 * @property CounterpartiesFileTypes               $doc_type
 * @property Cap                                   $cap
 * @property SettingLabels                         $label
 * @property Req3TasksDataItemDocs                 $doc_link
 * @property Req3TasksDataItemCommunicationChannels $communication_channel_link
 * @property Req3Templates                         $template
 * @property UsersAdmBlockReasons                  $block_reason
 * @property Req3TasksDataItemSteps[]              $template_steps
 * @property Req3TasksDataItemBaskets              $basket
 * @property Req3WorkRaterOperConfirmation         $work_rater_oper_confirmation
 * @property Req3TasksDataItemIdentifierComments[] $check_identifier_comments
 * @property Req3Identifiers                       $check_identifier
 * @property Req3WorkRaters                        $check_work
 * @property Req3RewardServices                    $reward_service
 * @property Request                               $crash
 * @property Req3TaskRewardWork                    $reward
 * @property ValuableFinalProducts                 $vfp
 * @property Req3TasksDataItemProjectTree          $node
 * @property Req3QueueLabels                       $queue_label
 * @property Req3RewardItems                       $reward_bonus_item
 * @property CounterpartiesFiles                   $archive_doc
 * @property Req3TasksDataItemsOperRoleComment     $oper_role_comment
 * @property Balance                               $balance
 * @property Req3TasksDataItemWhBalance[]          $balanceItems
 * @property Req3WorkRaters                        $workRater
 * @property Reasons                               $crashReason
 * @property Task                                  $order
 * @property Req3TasksDataLikes[]                  $likes
 */
class Req3TasksDataItems extends ActiveRecord
{
    // ============================================================================
    // ============================== КОНСТАНТЫ ===================================
    // ============================================================================
    const LINK_TYPE_TASK               = 1;
    const LINK_TYPE_SCHEDULER = 2;

    const VALUE_NUMBER_TYPE_CHECK_IDENTIFIER = 1;
    const VALUE_NUMBER_TYPE_CHECK_WORK = 2;
    const VALUE_NUMBER_TYPE_CHECK_STEP = 3;

    // ============================================================================
    // ============================== ДОПОЛНИТЕЛЬНЫЕ ПОЛЯ =========================
    // ============================================================================
    public $deferred_key;

    // ============================================================================
    // ============================== ИНИТ ========================================
    // ============================================================================
    public static function getDb()
    {
        return Yii::$app->get('db_mb4');
    }

    public static function tableName()
    {
        return 'req3_tasks_data_items';
    }

    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'parent_id'     => 'Parent ID',
            'link_id'       => 'Link ID',
            'link_type'     => 'Link Type',
            'identifier_id' => 'Идентификатор',
            'type'          => 'Тип',
            'value_id'      => "Данные",
            'value_text'    => 'Текст',
            'value_text_idx' => 'Текст (сокращенная версия для индексации)',
            'value_number'  => 'Число',
            'oper_id'       => 'oper_id',
            'is_deleted'    => 'is_deleted',
            'step_back_set' => 'step_back_set',
            'date_add'      => 'date_add',
        ];
    }

    public static function find()
    {
        return new _query\Req3TasksDataItemsQuery(get_called_class());
    }

    // ============================================================================
    // ============================== ПРАВИЛА =====================================
    // ============================================================================

    public function rules()
    {
        return [
            [['value_text'], 'filterText'],

            ['value_id', 'required', 'when' => fn($model) => $model->isDataValueId()],
            ['value_text', 'required', 'when' => fn($model) => $model->isDataValueText()],
            ['value_number', 'required', 'when' => fn($model) => $model->isDataValueNumber()],

            [['is_deleted'], 'default', 'value' => 0],
            [['step_back_set'], 'default', 'value' => 0],

            [['link_id', 'link_type', 'identifier_id', 'type', 'oper_id', 'date_add'], 'required'],
            [['parent_id', 'link_id', 'link_type', 'identifier_id', 'type', 'value_id', 'is_deleted', 'step_back_set'], 'integer'],
            [['value_text'], 'string'],
            [['value_number'], 'number'],

            [['value_id'], 'validateLink'],
            [['value_text'], 'validateText'],

            [['deferred_key', 'value_text_idx'], 'safe'],
        ];
    }

    public function filterText($attribute, $params)
    {
        if ($this->value_text !== null) {
            $this->value_text = trim($this->value_text);
            $this->value_text = mb_ereg_replace("[ ]+", " ", $this->value_text);
        }

        if (empty($this->value_text)) {
            $this->value_text = null;
        }
    }

    public function validateLink($attribute, $params)
    {
        if ($this->type == Req3Identifiers::TYPE_TEXT_PHONE) {
            $exist = $this->getPhone()->exists();
            if (!$exist) $this->addError($attribute, "Телефон не найден");
        }
        if ($this->type == Req3Identifiers::TYPE_COMMUNICATION_CHANNELS) {
            $exist = $this->getCommunication_channel_link()->exists();
            if (!$exist) $this->addError($attribute, "Связь с каналом связи не найдена");
        }
        if ($this->type == Req3Identifiers::TYPE_USER) {
            $exist = $this->getUser()->exists();
            if (!$exist) $this->addError($attribute, "Абонент не найден");
        }
        if ($this->type == Req3Identifiers::TYPE_USER_TYPE_CONNECT) {
            $types = Users::CONN_TYPE_NAMES;
            $exist = isset($types[$this->value_id]);
            if (!$exist) $this->addError($attribute, "Тип подключения не найден");
        }
        if ($this->type == Req3Identifiers::TYPE_USER_TYPE) {
            $types = Users::USER_TYPE_NAMES;
            $exist = isset($types[$this->value_id]);
            if (!$exist) $this->addError($attribute, "Тип абонента не найден");
        }
        if ($this->type == Req3Identifiers::TYPE_COUNTERPARTIES_DIADOK_DOCUMENT_STATUS) {
            $types = CounterpartiesFileSetting::DIADOK_DOCUMENT_STATUSES;
            $exist = isset($types[$this->value_text]);
            if (!$exist) $this->addError($attribute, "Статус документа не найден");
        }
        if ($this->type == Req3Identifiers::TYPE_COUNTERPARTIES_DIADOK_DOCUMENT_TYPE) {
            $types = CounterpartiesFileSetting::DIADOK_DOCUMENT_TYPES;
            $exist = isset($types[$this->value_text]);
            if (!$exist) $this->addError($attribute, "Тип документа не найден");
        }
        if ($this->type == Req3Identifiers::TYPE_USER_BLOCK_REASON) {
            $exist = $this->getBlock_reason()->exists();
            if (!$exist) $this->addError($attribute, "Причина блокировки не найдена");
        }
        if ($this->type == Req3Identifiers::TYPE_USER_CREDIT) {
            $types = UserCreditTypes::NAMES;
            $exist = isset($types[$this->value_id]);
            if (!$exist) $this->addError($attribute, "Тип рассрочки не найден");
        }
        if ($this->type == Req3Identifiers::TYPE_USER_KNOWN_FROM) {
            $exist = $this->getKnown_from()->exists();
            if (!$exist) $this->addError($attribute, "[Откуда узнал] не найдено");
        }
        if ($this->type == Req3Identifiers::TYPE_USER_CONNECT_FROM) {
            $types = UserConnectionStatusTypes::NAMES;
            $exist = isset($types[$this->value_id]);
            if (!$exist) $this->addError($attribute, "Статус подключения не найден");
        }
        if ($this->type == Req3Identifiers::TYPE_ADDRESS) {
            $exist = $this->getAddress()->exists();
            if (!$exist) $this->addError($attribute, "Адрес не найден");
        }
        if ($this->type == Req3Identifiers::TYPE_ADDRESS_CAP) {
            $exist = $this->getCap()->exists();
            if (!$exist) $this->addError($attribute, "ТКД не найдена");
        }
        if ($this->type == Req3Identifiers::TYPE_ADDRESS_ADD) {
            $exist = $this->getAddress()->exists();
            if (!$exist) $this->addError($attribute, "Адрес не найден");
        }
        if ($this->type == Req3Identifiers::TYPE_ADDRESS_ANY) {
            $exist = $this->address_link;
            if (!$exist) $this->addError($attribute, "Адрес не найден");
        }
        if ($this->type == Req3Identifiers::TYPE_ADDRESS_TREE_COVERAGE) {
            $exist = $this->address_link;
            if (!$exist) $this->addError($attribute, "Адрес не найден");
        }
        if ($this->type == Req3Identifiers::TYPE_ADDRESS_SETTING_LABEL) {
            $exist = $this->getLabel()->exists();
            if (!$exist) $this->addError($attribute, "Метка не найдена");
        }
        if ($this->type == Req3Identifiers::TYPE_FIN_MANAGER) {
            $exist = $this->getFm()->exists();
            if (!$exist) $this->addError($attribute, "Фин. менеджер не найден");
        }
        if ($this->type == Req3Identifiers::TYPE_UTM_TARIFF) {
            $exist = $this->getTariff()->exists();
            if (!$exist) $this->addError($attribute, "Тариф не найден");
        }
        if ($this->type == Req3Identifiers::TYPE_UTM_SERVICE) {
            $exist = $this->getService()->exists();
            if (!$exist) $this->addError($attribute, "Услуга не найдена");
        }
        if ($this->type == Req3Identifiers::TYPE_UTM_DP) {
            $exist = $this->getDp()->exists();
            if (!$exist) $this->addError($attribute, "Расчетный период не найден");
        }
        if ($this->type == Req3Identifiers::TYPE_UTM_DP_MONTH_AUTO) {
            $exist = $this->value_id == -1 || $this->getDp()->exists();
            if (!$exist) $this->addError($attribute, "Расчетный период не найден");
        }
        if ($this->type == Req3Identifiers::TYPE_REWARD_SERVICE) {
            $exist = $this->getReward_service()->exists();
            if (!$exist) $this->addError($attribute, "Услуга БП не найдена");
        }
        if ($this->type == Req3Identifiers::TYPE_WH_ITEM) {
            $exist = $this->getItem()->exists();
            if (!$exist) $this->addError($attribute, "Наименование не найдено");
        }
        if ($this->type == Req3Identifiers::TYPE_WH_ITEM_SIMPLE) {
            $exist = $this->getItem()->exists();
            if (!$exist) $this->addError($attribute, "Наименование не найдено");
        }
        if ($this->type == Req3Identifiers::TYPE_WH_SYN_ITEM) {
            $exist = $this->getSyn_item()->exists();
            if (!$exist) $this->addError($attribute, "Наименование не найдено");
        }
        if ($this->type == Req3Identifiers::TYPE_WH_WAREHOUSE) {
            $exist = $this->getWarehouse()->exists();
            if (!$exist) $this->addError($attribute, "Склад не найден");
        }
        if ($this->type == Req3Identifiers::TYPE_WH_TEMPLATE) {
            $exist = $this->getWh_template()->exists();
            if (!$exist) $this->addError($attribute, "Шаблон не найден");
        }
        if ($this->type == Req3Identifiers::TYPE_OPER) {
            $exist = $this->getOper()->exists();
            if (!$exist) $this->addError($attribute, "Исполнитель не найден");
        }
        if ($this->type == Req3Identifiers::TYPE_OPER_ROLE) {
            $exist = $this->getOper()->exists();
            if (!$exist) $this->addError($attribute, "Исполнитель не найден");
            $exist = Yii::$app->authManager->getRole($this->value_text);
            if (!$exist) $this->addError($attribute, "Запись о роли не найдена");
        }
        if ($this->type == Req3Identifiers::TYPE_FILE_1) {
            $exist = $this->getFile()->exists();
            if (!$exist) $this->addError($attribute, "Запись о файле не найдена");
        }
        if ($this->type == Req3Identifiers::TYPE_LIST) {
            $exist = $this->getList_item()->exists();
            if (!$exist) $this->addError($attribute, "Запись о элементе списка не найдена");
        }
        if ($this->type == Req3Identifiers::TYPE_LIST_TREE) {
            $exist = $this->getList_tree_item()->exists();
            if (!$exist) $this->addError($attribute, "Запись о элементе вложенного списка не найдена");
        }
        if ($this->type == Req3Identifiers::TYPE_ROLE) {
            $exist = Yii::$app->authManager->getRole($this->value_text);
            if (!$exist) $this->addError($attribute, "Запись о роли не найдена");
        }
        if ($this->type == Req3Identifiers::TYPE_COUNTERPARTIES) {
            $exist = $this->getCounterparty()->exists();
            if (!$exist) $this->addError($attribute, "Запись о контрагенте не найдена");
        }
        if ($this->type == Req3Identifiers::TYPE_COUNTERPARTIES_DOCUMENTS) {
            $exist = $this->getDoc_link()->exists();
            if (!$exist) $this->addError($attribute, "Запись о связке с документами не найдена");
        }
        if ($this->type == Req3Identifiers::TYPE_CALL_NAMES) {
            $exist = $this->getCall_name()->exists();
            if (!$exist) $this->addError($attribute, "Запись о названии линии не найдена");
        }
        if ($this->type == Req3Identifiers::TYPE_DOCUMENT_TYPE) {
            $exist = $this->getDoc_type()->exists();
            if (!$exist) $this->addError($attribute, "Запись о типе документа не найдена");
        }
        if ($this->type == Req3Identifiers::TYPE_STATUS_ZONE) {
            $types = SettingStatusZone::NAMES;
            $exist = isset($types[$this->value_id]);
            if (!$exist) $this->addError($attribute, "Статус зоны покрытия не найден");
        }
        if ($this->type == Req3Identifiers::TYPE_TEMPLATES) {
            $exist = $this->getTemplate()->exists();
            if (!$exist) $this->addError($attribute, "Шаблон не найден");
        }
        if ($this->type == Req3Identifiers::TYPE_VFP_LIST) {
            $exist = $this->getVfp()->exists();
            if (!$exist) $this->addError($attribute, "ЦКП не найдено");
        }
        if ($this->type == Req3Identifiers::TYPE_CHECK_IDENTIFIER) {
            if ($this->value_number == self::VALUE_NUMBER_TYPE_CHECK_IDENTIFIER) {
                $exist = $this->getCheck_identifier()->exists();
                if (!$exist) $this->addError($attribute, "Проверяемый идентификатор не найден");
            }
            if ($this->value_number == self::VALUE_NUMBER_TYPE_CHECK_WORK) {
                $exist = $this->getCheck_work_rated()->exists();
                if (!$exist) $this->addError($attribute, "Проверяемая работа не найдена");
            }
        }
        if ($this->type == Req3Identifiers::TYPE_SERVICE_BASKET) {
            $exist = $this->getBasket()->exists();
            if (!$exist) $this->addError($attribute, "Корзина не найдена");
        }
        if ($this->type == Req3Identifiers::TYPE_CRASH) {
            $exist = $this->getCrash()->exists();
            if (!$exist) $this->addError($attribute, "Авария не найдена");
        }
        if ($this->type == Req3Identifiers::TYPE_CRASH_REASONS) {
            $exist = $this->getCrashReason()->exists();
            if (!$exist) $this->addError($attribute, "Причина аварии не найдена");
        }
        if ($this->type == Req3Identifiers::TYPE_QUEUE_LABEL) {
            $exist = $this->getQueue_label()->exists();
            if (!$exist) $this->addError($attribute, "Метка не найдена");
        }
        if ($this->type == Req3Identifiers::TYPE_BONUS_REWARD) {
            $exist = $this->getReward_bonus_item()->exists();
            if (!$exist) $this->addError($attribute, "Бонус/штраф не найден");
        }
        if ($this->type == Req3Identifiers::TYPE_COUNTERPARTIES_ARCHIVE_DOC) {
            $exist = $this->getArchive_doc()->exists();
            if (!$exist) $this->addError($attribute, "Документ не найден");
        }
        if ($this->type == Req3Identifiers::TYPE_WH_BALANCE) {
            $exist = $this->getBalance()->exists();
            if (!$exist) $this->addError($attribute, "Баланс не найден");
            if ($this->identifier) {
                $whNeedItems = $this->identifier->getSettingByKey(Req3Identifiers::SETTING_WH_NEED_ITEMS, false);
                if ($whNeedItems) {
                    if (count($this->balanceItems) == 0) {
                        $this->addError($attribute, "Выберите наименования баланса");
                    }
                }
            }
        }
        if ($this->type == Req3Identifiers::TYPE_WORK_RATER) {
            $exist = $this->getWorkRater()->exists();
            if (!$exist) $this->addError($attribute, "Работа не найдена");
        }
        if ($this->type == Req3Identifiers::TYPE_ORDER) {
            $exist = $this->getOrder()->exists();
            if (!$exist) $this->addError($attribute, "Наряд не найден");
        }

    }

    public function validateText($attribute, $params)
    {
        if ($this->type == Req3Identifiers::TYPE_TEXT) {
            if ($this->identifier && $this->identifier->regexp) {

                if (!$this->identifier->regexp->isCorrect()) {
                    $this->addError($attribute, "Регулярное выражение не корректно, обратитесь к администратору. (id{$this->identifier->regexp->id})");
                } else {
                    $this->value_text = trim($this->value_text);
                    if (!$this->identifier->regexp->check($this->value_text)) {
                        $error = "Введенный текст не соответствует правилам";
                        $error .= "<br>" . $this->identifier->regexp->info;

                        $examples = explode("\n", $this->identifier->regexp->examples);
                        if (count($examples) > 0) {
                            $error .= "<br>Примеры:";
                            foreach ($examples as $example) {
                                $example = trim($example);
                                if (!empty($example)) {
                                    if ($this->identifier->regexp->check($example)) {
                                        $error .= "<br><span style='color: #00990c'>" . $example . "</span> ";
                                    } else {
                                        $error .= "<br><span style='color: #e00f07; text-decoration-line: line-through'>" . $example . "</span> ";
                                    }
                                }
                            }
                        }

                        $this->addError($attribute, $error);
                    }
                }
            }
        }

        if (in_array($this->type, [Req3Identifiers::TYPE_TEXT_DATE, Req3Identifiers::TYPE_TEXT_DATE_TIME])) {
            if ($this->identifier) {

                $anyOk = false;
                $formats = ['Y-m-d H:i', 'Y-m-d', 'Y-m-d H:i:s'];
                foreach ($formats as $format) {
                    $d = DateTime::createFromFormat($format, $this->value_text);
                    if ($d && $d->format($format) == $this->value_text) {
                        $anyOk = true;
                        break;
                    }
                }

                if (!$anyOk) {
                    $this->addError($attribute, "Ошибка формата даты. ");
                } else {
                    $setting = $this->identifier->getSettingArray();
                    $days = isset($setting['days']) ? $setting['days'] : '';
                    if (!empty($days)) {
                        $end = new Date();
                        $end->addDays($days);

                        $now = new Date();

                        if ($this->type == Req3Identifiers::TYPE_TEXT_DATE) {
                            $date = new Date($this->value_text . " " . $now->format(Date::FORMAT_TIME_SECOND));
                        } else {
                            $date = new Date($this->value_text);
                        }

                        if ($date->getTimeStamp() < $now->getTimeStamp() || $date->getTimeStamp() > $end->getTimeStamp()) {
                            $this->addError($attribute, "Стоит ограничение на ввод даты: от сегодня до " . $end->format(Date::FORMAT_DATE_TIME_DB));
                        }
                    }
                }
            }
        }
    }

    // ============================================================================
    // ============================== СЦЕНАРИИ ====================================
    // ============================================================================

    // ============================================================================
    // ============================== ГЕТТЕРЫ =====================================
    // ============================================================================
    public function getTask()
    {
        return $this->hasOne(Req3Tasks::class, ['id' => 'link_id']);
    }

    public function getScheduler()
    {
        return $this->hasOne(Req3Scheduler::class, ['id' => 'link_id']);
    }

    public function getChildren()
    {
        return $this->hasMany(Req3TasksDataItems::class, ['parent_id' => 'id']);
    }

    public function getIdentifier()
    {
        return $this->hasOne(Req3Identifiers::class, ['id' => 'identifier_id']);
    }

    public function getOper_add()
    {
        return $this->hasOne(Opers::class, ['oper_id' => 'oper_id']);
    }

    public function getUser()
    {
        return $this->hasOne(Users::class, ['user_id' => 'value_id']);
    }

    public function getKnown_from()
    {
        return $this->hasOne(UsersConnectKnownFrom::class, ['kf_id' => 'value_id']);
    }

    public function getAddress()
    {
        return $this->hasOne(MapAddresses::class, ['addr_id' => 'value_id']);
    }

    public function getFm()
    {
        return $this->hasOne(FinManagers::class, ['fm_id' => 'value_id']);
    }

    public function getTariff()
    {
        return $this->hasOne(Tariffs::class, ['id' => 'value_id']);
    }

    public function getService()
    {
        return $this->hasOne(ServicesData::class, ['id' => 'value_id']);
    }

    public function getDp()
    {
        return $this->hasOne(DiscountPeriods::class, ['static_id' => 'value_id'])->onCondition(['is_expired' => 0]);
    }

    public function getPhone()
    {
        return $this->hasOne(Phones::class, ['id' => 'value_id']);
    }

    public function getItem()
    {
        return $this->hasOne(Item::class, ['item_id' => 'value_id']);
    }

    public function getSyn_item()
    {
        return $this->hasOne(ItemSyn::class, ['syn_id' => 'value_id']);
    }

    public function getWarehouse()
    {
        return $this->hasOne(Warehouse::class, ['whs_id' => 'value_id']);
    }

    public function getWh_template()
    {
        return $this->hasOne(Template::class, ['id' => 'value_id']);
    }

    public function getBalance()
    {
        return $this->hasOne(Balance::class, ['bal_id' => 'value_id']);
    }

    public function getOper()
    {
        return $this->hasOne(Opers::class, ['oper_id' => 'value_id']);
    }

    public function getFile()
    {
        return $this->hasOne(Req3TasksDataFiles::class, ['id' => 'value_id']);
    }

    public function getList_item()
    {
        return $this->hasOne(ListsItems::class, ['id' => 'value_id']);
    }

    public function getList_tree_item()
    {
        return $this->hasOne(ListsTreeItems::class, ['id' => 'value_id']);
    }

    public function getCounterparty()
    {
        return $this->hasOne(Counterparties::class, ['id' => 'value_id']);
    }

    public function getCall_name()
    {
        return $this->hasOne(CalledPhones::class, ['id' => 'value_id']);
    }

    public function getDoc_type()
    {
        return $this->hasOne(CounterpartiesFileTypes::class, ['id' => 'value_id']);
    }

    public function getCap()
    {
        return $this->hasOne(Cap::class, ['cap_id' => 'value_id']);
    }

    public function getLabel()
    {
        return $this->hasOne(SettingLabels::class, ['id' => 'value_id']);
    }

    public function getAddress_link()
    {
        return $this->hasOne(Req3TasksDataItemAddress::class, ['item_id' => 'id']);
    }

    public function getDoc_link()
    {
        return $this->hasOne(Req3TasksDataItemDocs::class, ['id' => 'value_id']);
    }

    public function getCommunication_channel_link()
    {
        return $this->hasOne(Req3TasksDataItemCommunicationChannels::class, ['id' => 'value_id']);
    }

    public function getTemplate()
    {
        return $this->hasOne(Req3Templates::class, ['id' => 'value_id']);
    }

    public function getBlock_reason()
    {
        return $this->hasOne(UsersAdmBlockReasons::class, ['id' => 'value_id']);
    }

    public function getTemplate_steps()
    {
        return $this->hasMany(Req3TasksDataItemSteps::class, ['item_id' => 'id']);
    }

    public function getWork_rater_oper_confirmation()
    {
        return $this->hasOne(Req3WorkRaterOperConfirmation::class, ['id' => 'value_id']);
    }

    public function getCheck_identifier_comments()
    {
        return $this->hasMany(Req3TasksDataItemIdentifierComments::class, ['item_id' => 'id']);
    }

    public function getCheck_identifier()
    {
        return $this->hasOne(Req3Identifiers::class, ['id' => 'value_id']);
    }

    public function getCheck_work_rated()
    {
        return $this->hasOne(Req3WorkRaters::class, ['id' => 'value_id']);
    }

    public function getReward_service()
    {
        return $this->hasOne(Req3RewardServices::class, ['id' => 'value_id']);
    }

    public function getBasket()
    {
        return $this->hasOne(Req3TasksDataItemBaskets::class, ['id' => 'value_id']);
    }

    public function getCrash()
    {
        return $this->hasOne(Request::class, ['req_id' => 'value_id']);
    }

    public function getReward()
    {
        return $this->hasOne(Req3TaskRewardWork::class, ['id' => 'value_id']);
    }

    public function getVfp()
    {
        return $this->hasOne(ValuableFinalProducts::class, ['id' => 'value_id']);
    }

    public function getNode()
    {
        return $this->hasOne(Req3TasksDataItemProjectTree::class, ['id' => 'value_id']);
    }

    public function getQueue_label()
    {
        return $this->hasOne(Req3QueueLabels::class, ['id' => 'value_id']);
    }

    public function getReward_bonus_item()
    {
        return $this->hasOne(Req3RewardItems::class, ['id' => 'value_id']);
    }

    public function getArchive_doc()
    {
        return $this->hasOne(CounterpartiesFiles::class, ['id' => 'value_id']);
    }

    public function getOper_role_comment()
    {
        return $this->hasOne(Req3TasksDataItemsOperRoleComment::class, ['item_id' => 'id']);
    }

    public function getBalanceItems()
    {
        return $this->hasMany(Req3TasksDataItemWhBalance::class, ['item_id' => 'id']);
    }

    public function getWorkRater()
    {
        return $this->hasOne(Req3WorkRaters::class, ['id' => 'value_id']);
    }

    public function getCrashReason()
    {
        return $this->hasOne(Reasons::class, ['reason_id' => 'value_id']);
    }

    public function getOrder()
    {
        return $this->hasOne(Task::class, ['id' => 'value_id']);
    }

    public function getLikes()
    {
        return $this->hasMany(Req3TasksDataLikes::class, ['item_id' => 'id']);
    }

    // ============================================================================
    // ============================== СЕТТЕРЫ =====================================
    // ============================================================================

    // ============================================================================
    // ============================== ДОГРУЗКА СВЯЗЕЙ =============================
    // ============================================================================
    public static function preLoadFiles(&$data, $key = null)
    {
        $fnct_load = function (&$data, $names, $temp_names, $types, $sizes, $errors, $full_paths, $temp_resources) use (&$fnct_load) {
            if ($names == null) return;
            if ($temp_names == null) return;
            if ($types == null) return;
            if ($sizes == null) return;
            if ($errors == null) return;

            if (($data['type'] ?? null) == Req3Identifiers::TYPE_FILE_1 && isset($names['file'])) {
                $temp_resource = $temp_resources['file'] ?? null;
                $full_path = $full_paths['file'] ?? null;
                $_file = [
                    'name'     => $names['file'],
                    'tempName' => $temp_names['file'],
                    'tempResource' => is_resource($temp_resource) ? $temp_resource : null,
                    'type'     => $types['file'],
                    'size'     => $sizes['file'],
                    'error'    => $errors['file'],
                    'fullPath' => is_string($full_path) ? $full_path : null,
                ];

                $file = new UploadedFile($_file);

                $directory = Yii::getAlias("@app/modules/process/files/tasks/");
                $path = date("Y") . "/" . date("m") . "/" . date("d") . "/";
                $symbol = "qwertyuiopasdfghjklzxcvbnm1234567890";
                $prefix = "file_" . date("Ymd_His") . "_";
                do {
                    $lang_name = mt_rand(5, 10);
                    $name = $prefix;
                    for ($i = 0; $i < $lang_name; $i++) {
                        $name .= $symbol[mt_rand(0, strlen($symbol) - 1)];
                    }
                    $name .= "." . $file->extension;
                } while (file_exists($directory . $path . $name));
                if (!is_dir($directory . $path)) mkdir($directory . $path, 0777, true);
                if ($file->saveAs($directory . $path . $name, false)) {
                    $file_new = new Req3TasksDataFiles();
                    $file_new->orig_name = $file->name;
                    $file_new->save_name = $name;
                    $file_new->path = $path;
                    if ($file_new->save()) {
                        $data['value_id'] = $file_new->id;
                    }
                }
            } else {
                foreach ($names as $key => $sub_names) {
                    if (is_array($sub_names) && isset($data[$key])) {
                        $fnct_load(
                            $data[$key],
                            $sub_names,
                            $temp_names[$key] ?? null,
                            $types[$key] ?? null,
                            $sizes[$key] ?? null,
                            $errors[$key] ?? null,
                            $full_paths[$key] ?? [],
                            $temp_resources[$key] ?? []
                        );
                    }
                }
            }
        };

        if (isset($_FILES)) {
            foreach ($_FILES as $k => $info) {
                if ($key == $k || $key === null) {
                    $data_item = null;
                    if ($key === null) {
                        if (isset($data[$k])) {
                            $data_item =& $data[$k];
                        }
                    } else {
                        $data_item =& $data;
                    }
                    if ($data_item) {
                        $fnct_load(
                            $data_item,
                            $info['name'] ?? null,
                            $info['tmp_name'] ?? null,
                            $info['type'] ?? null,
                            $info['size'] ?? null,
                            $info['error'] ?? null,
                            $info['full_path'] ?? [],
                            $info['tmp_resource'] ?? []
                        );
                    }
                }
            }
        }
    }

    /** не забыть про шедулер */
    public function load($data, $formName = "", $oper_id = null, $is_test_not_save = false)
    {
        parent::load($data, $formName);

        if ($this->type == Req3Identifiers::TYPE_TEMPLATE_STEPS) {
            if (isset($data['template_steps'])) {
                $items = [];
                foreach ($data['template_steps'] as $id) {
                    $item = new Req3TasksDataItemSteps();
                    if (is_string($id) && $id[0] == 'c')
                        $item->category_id = intval(substr($id, 1));
                    elseif (is_string($id) && $id[0] == 'v')
                        $item->version_id = intval(substr($id, 1));
                    else
                        $item->step_id = intval($id);

                    $items[] = $item;
                }
                $this->populateRelation('template_steps', $items);
            }
        }

        if ($this->type == Req3Identifiers::TYPE_TEXT_PHONE) {
            if (isset($data['value_phone']) && isset($data['value_name'])) {
                try {
                    $phone = Phones::getOrAdd($data['value_phone'], $data['value_name']);
                    $this->value_id = $phone->id;
                } catch (Exception $e) {
                    $phone = new Phones();
                    $phone->phone = $data['value_phone'];
                    $phone->fio = $data['value_name'];
                    $this->value_id = null;
                    $this->addError('other', $e->getMessage());
                }
                $this->populateRelation('phone', $phone);
            }
        }

        if ($this->type == Req3Identifiers::TYPE_COMMUNICATION_CHANNELS) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!isset($data['object']['channelType'])) {
                    throw new Exception("Нет данных канала");
                }
                if (isset($data['object']['channelId'])) {
                    $info = new ChannelLinkedDto(null, $data['object']['channelId'], $data['object']['channelType'], CommunicationTypes::OTHER, '');
                } else {
                    /** @var ModuleCommunication $communicationModule */
                    $communicationModule = Yii::$app->getModule('communication');
                    $form = $communicationModule->getFormChannelEdit($data['object']['channelType']);
                    $form->load($data['object'], '');
                    if (!$form->validate()) {
                        throw new Exception("Не удалось сохранить коммуникацию: " . implode(', ', $form->getFirstErrors()));
                    }
                    $info = $form->toDto();
                }

                $link = new Req3TasksDataItemCommunicationChannels();
                $link->channel_id = $info->channelId;
                $link->channel_type = $info->channelType;
                $link->type_id = $info->typeId;
                $link->comment = !empty($info->comment) ? $info->comment : "";

                if (!$is_test_not_save) {
                    if (!$link->save()) {
                        throw new \yii\db\Exception("Не удалось сохранить коммуникацию: " . implode(', ', $link->getFirstErrors()));
                    }
                }

                $this->value_id = $link->id;
                $this->populateRelation('communication_channel_link', $link);

                $transaction->commit();
            } catch (Exception $e) {
                $this->addError('other', $e->getMessage());
                $transaction->rollBack();
            }
        }

        if ($this->type == Req3Identifiers::TYPE_USER_KNOWN_FROM) {
            if ($this->value_id == 18) {
                $this->value_text = null;
                if (isset($data['value_oper_id'])) {
                    $oper = Opers::find()->id($data['value_oper_id'])->one();
                    if ($oper) {
                        $this->value_text = (string)$oper->oper_id;
                    }
                }
            }
        }

        if ($this->type == Req3Identifiers::TYPE_ADDRESS_ADD) {
            if (!empty($data['street_id']) && !empty($data['house'])) {
                $addressService = Yii::$container->get(AddressService::class);
                try {
                    $streetId = $data['street_id'];
                    $house = trim($data['house']);
                    $housing = $data['housing'] ?? null;
                    $flat = $data['flat'] ?? null;
                    $isPremises = ($data['is_premises'] ?? null) == 1;
                    $dopData = [
                        'cadastral_number'      => $data['cadastral_number'] ?? null,
                        'coordinates'           => $data['coordinates'] ?? null,
                        AddressAddData::WHO_ADD => $oper_id,
                        AddressAddData::COMMENT => "Из БП {$this->link_id} ($this->link_type)",
                        AddressAddData::IS_PREMISES => $isPremises,
                    ];
                    $address = $addressService->getOrAdd($streetId, $house, $housing, $flat, $dopData);

                    $this->value_id = $address->addr_id;
                    $this->populateRelation('address', $address);
                } catch (Exception $e) {
                    $this->addError('other', $e->getMessage());
                }
            }
        }

        if ($this->type == Req3Identifiers::TYPE_COUNTERPARTIES_DOCUMENTS) {

            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (isset($data['doc_type'])) {

                    $doc_link = new Req3TasksDataItemDocs(['doc_type' => $data['doc_type']]);

                    if (isset($data['doc_id'])) {
                        $doc_link->doc_id = $data['doc_id'];
                    }

                    // TODO - перевести на сервис контрагентов по обновлению документа
                    if ($doc_link->isAccessEdit($this)) {
                        $types = [
                            Req3TasksDataItemDocs::DOC_TYPE_PASSPORT_RF => ['class' => DocPassportRf::class, 'relation' => "doc_passport_rf", 'key' => "DocPassportRf"],
                            Req3TasksDataItemDocs::DOC_TYPE_RESIDENCE => ['class' => DocResidence::class, 'relation' => "doc_residence", 'key' => "DocResidence"],
                            Req3TasksDataItemDocs::DOC_TYPE_RWP       => ['class' => DocRwp::class, 'relation' => "doc_rwp", 'key' => "DocRwp"]
                        ];

                        $type = $types[$doc_link->doc_type] ?? null;
                        if ($type) {
                            /** @var DocPassportRf|DocResidence|DocRwp $doc */
                            $doc = null;
                            if (!empty($doc_link->doc_id)) {
                                $doc = $doc_link->{$type['relation']};
                            }
                            if (!$doc) {
                                $doc_link->doc_id = null;
                                $doc = new $type['class']();
                            }

                            $doc->load($data[$type['key']] ?? [], '');

                            /** @var DocPassportRf|DocResidence|DocRwp $duplicate */
                            $duplicate = $doc->findDuplicateThis();
                            if ($duplicate) {
                                if ($duplicate->doc_link || $duplicate->phys_face) {
                                    throw new Exception("Документ уже есть, с привязкой");
                                }
                                $duplicate->load($data[$type['key']] ?? [], '');
                                $doc = $duplicate;
                            }

                            if ($doc->save()) {
                                $doc_link->doc_id = $doc->id;
                                $doc_link->populateRelation($type['relation'], $doc);
                            } else {
                                Yii::warning($doc->getFirstErrors());
                                throw new Exception(implode(', ', $doc->getFirstErrors()));
                            }
                        }
                    }

                    //сохраняем связь с документом и отдаем ид к привязке
                    if (!$is_test_not_save) {
                        if ($doc_link->save()) {
                            $this->value_id = $doc_link->id;
                        } else {
                            throw new Exception(implode(', ', $doc_link->getFirstErrors()));
                        }
                    }

                    $this->populateRelation('doc_link', $doc_link);
                    //-------------------------

                } else {
                    throw new Exception("Нет данных документа");
                }
                $transaction->commit();
            } catch (Exception $e) {
                $this->addError('other', $e->getMessage());
                $transaction->rollBack();
            }
        }

        if ($this->type == Req3Identifiers::TYPE_LIST) {
            if ($this->identifier && $this->identifier->list_group && $this->identifier->list_group->is_complemented) {
                if (!empty($this->value_id) && (!$this->list_item || $this->list_item->group_id != $this->identifier->list_group->id)) {
                    $item = new ListsItems();
                    $item->group_id = $this->identifier->type_info;
                    $item->value = $this->value_id;
                    $item->is_approved = 0;
                    $item->oper_added = $oper_id;
                    if (!$is_test_not_save) {
                        if ($item->save()) {
                            $this->value_id = $item->id;
                        } else {
                            $this->value_id = null;
                            $this->addError('other', implode(", ", $item->getFirstErrors()));
                        }
                    }
                    $this->populateRelation('list_item', $item);
                }
            }
        }

        if ($this->type == Req3Identifiers::TYPE_LIST_TREE) {
            if ($this->identifier && $this->identifier->list_tree_group && $this->identifier->list_tree_group->is_complemented) {
                if (!empty($this->value_id) && (!$this->list_tree_item || $this->list_tree_item->group_id != $this->identifier->list_tree_group->id)) {
                    $item = new ListsTreeItems();
                    $item->group_id = $this->identifier->type_info;
                    $item->parent_id = $data['tree_parent_id'] ?? null;
                    $item->value = $this->value_id;
                    $item->is_approved = 0;
                    $item->oper_added = $oper_id;
                    $item->comment = $data['comment'] ?? "";
                    if (!$is_test_not_save) {
                        if ($item->save()) {
                            $this->value_id = $item->id;
                        } else {
                            $this->value_id = null;
                            $this->addError('other', implode(", ", $item->getFirstErrors()));
                        }
                    }
                    $this->populateRelation('list_tree_item', $item);
                }
            }
        }

        if ($this->type == Req3Identifiers::TYPE_ADDRESS_ANY) {
            if (!empty($this->value_id)) {
                $type = Address::getTypeById($this->value_id);
                $id = Address::getOrigIdById($this->value_id);
                $model = new Req3TasksDataItemAddress();
                if ($type == Address::TYPE_FLAT) $model->address_id = $id;
                if ($type == Address::TYPE_HOUSE) $model->house_id = $id;
                if ($type == Address::TYPE_STREET) $model->street_id = $id;
                if ($type == Address::TYPE_LOCALITY) $model->location_id = $id;
                $this->populateRelation('address_link', $model);
            }
        }

        if ($this->type == Req3Identifiers::TYPE_ADDRESS_TREE_COVERAGE) {
            if (!empty($this->value_id)) {
                $type = Address::getTypeById($this->value_id);
                $id = Address::getOrigIdById($this->value_id);
                $model = new Req3TasksDataItemAddress();
                if ($type == Address::TYPE_STREET) {
                    $model->street_id = $id;
                    $model->coverage = $model->street && $model->street->coverage ? $model->street->coverage->coverage : null;
                }
                if ($type == Address::TYPE_LOCALITY) {
                    $model->location_id = $id;
                    $model->coverage = $model->location && $model->location->coverage ? $model->location->coverage->coverage : null;
                }
                $this->populateRelation('address_link', $model);
            }
        }

        if ($this->type == Req3Identifiers::TYPE_OPER_ROLE) {
            $value_id = explode("_", $this->value_id ?? "", 2);
            $this->value_id = $value_id[0] ?? null;
            $this->value_text = $value_id[1] ?? $this->value_text;
        }

        if ($this->type == Req3Identifiers::TYPE_COUNTERPARTIES) {
            if (Str::sub($this->value_id, 0, 1) == '+') {
                $inn = Str::sub($this->value_id, 1);
                $this->value_id = null;
                unset($this->counterparty);

                $counterparty = Counterparties::find()->inn($inn)->one();
                if ($counterparty) {
                    $this->value_id = $counterparty->id;
                    $this->populateRelation('counterparty', $counterparty);
                } else {
                    $counterparty = Counterparties::findInn($inn);
                    if ($counterparty) {
                        $model = new FormAddCounterparty($counterparty);
                        if (!$is_test_not_save) {
                            if ($model->save()) {
                                $this->value_id = $model->getId();
                            } else {
                                $this->addError('other', "Ошибка добавление контрагента: " . implode(", ", $model->getFirstErrors()));
                            }
                        }
                        $this->populateRelation('counterparty', $model->getMainModel());
                    }
                }
            }
        }

        if ($this->type == Req3Identifiers::TYPE_WH_BALANCE) {
            $balanceItems = [];

            $whNeedItems = $this->identifier->getSettingByKey(Req3Identifiers::SETTING_WH_NEED_ITEMS, false);
            if ($whNeedItems) {
                $itemUitemIds = $data['item_uitem_ids'] ?? [];
                $details = $data['details'] ?? [];
                foreach ($itemUitemIds as $itemUitemId) {
                    list($itemId, $uItemId) = explode("_", $itemUitemId);
                    $balanceItems[] = new Req3TasksDataItemWhBalance(['wh_item_id' => $itemId, 'wh_uitem_id' => $uItemId, 'wh_count' => $details[$itemUitemId] ?? 1]);
                }
            }

            $this->populateRelation('balanceItems', $balanceItems);
        }

        return true;
    }

    // ============================================================================
    // ============================== СОБЫТИЯ СОХРАНЕНИЯ ==========================
    // ============================================================================
    public function beforeSave($insert)
    {
        if ($this->value_text == null) $this->value_text_idx = null;
        else $this->value_text_idx = Str::compactOverflow($this->value_text, 255, "");

        return parent::beforeSave($insert);
    }

    public function save($runValidation = true, $attributeNames = null, $cascade = false)
    {
        if (parent::save($runValidation, $attributeNames)) {
            if ($cascade) {
                //-----------------------------------------------------------
                if ($this->type == Req3Identifiers::TYPE_ADDRESS_ANY) {
                    if ($this->address_link) {
                        $this->address_link->item_id = $this->id;
                        $this->address_link->save();
                    }
                }
                //-----------------------------------------------------------
                if ($this->type == Req3Identifiers::TYPE_ADDRESS_TREE_COVERAGE) {
                    if ($this->address_link) {
                        $this->address_link->item_id = $this->id;
                        $this->address_link->save();
                    }
                }
                //-----------------------------------------------------------
                if ($this->type == Req3Identifiers::TYPE_TEMPLATE_STEPS) {
                    $template_steps = $this->template_steps;
                    Req3TasksDataItemSteps::deleteAll(['item_id' => $this->id]);
                    foreach ($template_steps as $template_step) {
                        $template_step->setIsNewRecord(true);
                        $template_step->item_id = $this->id;
                        $template_step->save();
                    }
                }
                //-----------------------------------------------------------
                if ($this->type == Req3Identifiers::TYPE_CHECK_IDENTIFIER) {
                    foreach ($this->check_identifier_comments as $item) {
                        $item->item_id = $this->id;
                        $item->save();
                    }
                }
                //-----------------------------------------------------------
                if ($this->type == Req3Identifiers::TYPE_WH_BALANCE) {
                    $balanceItems = $this->balanceItems;
                    Req3TasksDataItemWhBalance::deleteAll(['item_id' => $this->id]);
                    foreach ($balanceItems as $balanceItem) {
                        $balanceItem->setIsNewRecord(true);
                        $balanceItem->item_id = $this->id;
                        $balanceItem->save();
                    }
                }
                //-----------------------------------------------------------
            }
            //-----------------------------------------------------------
            foreach ($this->children as $child) {
                $child->parent_id = $this->id;
                $child->link_id = $this->link_id;
                $child->link_type = $this->link_type;

                $child->oper_id = $this->oper_id;
                $child->date_add = $this->date_add;
                $child->is_deleted = $this->is_deleted;
                if (!$child->save($runValidation, $attributeNames, $cascade)) {
                    throw new Exception("Ошибка сохранения вложенных данных (" . implode(", ", $child->getFirstErrors()) . ")");
                }
            }
            //-----------------------------------------------------------
            return true;
        }
        return false;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // при выборе элемента в древовидном списке, обновляем если есть незавершенный поиск, что выбрал человек после того как искал
        if ($this->type == Req3Identifiers::TYPE_LIST_TREE && $this->link_type == self::LINK_TYPE_TASK) {
            $select_item_id = $this->value_id;
            if (!empty($select_item_id)) {
                ListsTreeGroupsFindReq3::updateAll([
                    'after_select_item_id' => $select_item_id,
                    'after_selected_seconds' => new Expression("TIMESTAMPDIFF(SECOND, date_find, NOW())")
                ], [
                    'AND',
                    ['task_id' => $this->link_id],
                    ['identifier_id' => $this->identifier_id],
                    ['IS', 'after_select_item_id', null]
                ]);
            }
        }

        if (!empty($this->deferred_key)) {
            if ($this->link_type == self::LINK_TYPE_TASK) {
                $confirmations = Req3WorkRaterOperConfirmation::find()->operId($this->oper_id)->checkedObjectId($this->identifier_id)->key($this->deferred_key)->all();
                foreach ($confirmations as $confirmation) {
                    $confirmation->linkTask($this->link_id);
                }
            }
        }
    }
    // ============================================================================
    // ============================== СОБЫТИЯ УДАЛЕНИЯ ============================
    // ============================================================================
    public function beforeDelete()
    {
        if ($this->type == Req3Identifiers::TYPE_GROUP) {
            $items = $this->getChildren()->all();
            foreach ($items as $item) $item->delete();
        }

        if ($this->type == Req3Identifiers::TYPE_TEMPLATE_STEPS) {
            $items = $this->getTemplate_steps()->all();
            foreach ($items as $item) $item->delete();
        }

        if (in_array($this->type, [Req3Identifiers::TYPE_ADDRESS_ANY, Req3Identifiers::TYPE_ADDRESS_TREE_COVERAGE])) {
            $item = $this->getAddress_link()->one();
            if ($item) $item->delete();
        }

        if ($this->type == Req3Identifiers::TYPE_CHECK_IDENTIFIER) {
            $items = $this->getCheck_identifier_comments()->all();
            foreach ($items as $item) $item->delete();
        }

        if ($this->type == Req3Identifiers::TYPE_COMMUNICATION_CHANNELS) {
            $items = $this->getCommunication_channel_link()->all();
            foreach ($items as $item) $item->delete();
        }
        if ($this->type == Req3Identifiers::TYPE_WH_BALANCE) {
            $items = $this->getBalanceItems()->all();
            foreach ($items as $item) $item->delete();
        }

        $items = $this->getOper_role_comment()->all();
        foreach ($items as $item) $item->delete();

        /*if ($this->type == Req3Identifiers::TYPE_FILE_1) {
            $items = $this->getFile()->all();
            foreach ($items as $item) $item->delete();
        }*/

        /*if ($this->type == Req3Identifiers::TYPE_COUNTERPARTIES_DOCUMENTS) {
            $items = $this->getDoc_link()->all();
            foreach ($items as $item) $item->delete();
        }*/


        return parent::beforeDelete();
    }

    public static function deleteByTaskId(int $taskId)
    {
        $data = self::find()->where(['link_id' => $taskId, 'link_type' => self::LINK_TYPE_TASK])->select(['id', 'value_id', 'type'])->asArray()->all();
        $dataIds = ArrayHelper::getColumn($data, 'id');
        if (!empty($dataIds)) {
            Req3TasksDataItemsOperRoleComment::deleteAll(['item_id' => $dataIds]);

            $data = ArrayHelper::index($data, null, 'type');
            foreach ($data as $type => $values) {
                $ids = ArrayHelper::getColumn($values, 'id');

                if ($type == Req3Identifiers::TYPE_TEMPLATE_STEPS) {
                    Req3TasksDataItemSteps::deleteAll(['item_id' => $ids]);
                }

                if (in_array($type, [Req3Identifiers::TYPE_ADDRESS_ANY, Req3Identifiers::TYPE_ADDRESS_TREE_COVERAGE])) {
                    Req3TasksDataItemAddress::deleteAll(['item_id' => $ids]);
                }

                if ($type == Req3Identifiers::TYPE_CHECK_IDENTIFIER) {
                    Req3TasksDataItemIdentifierComments::deleteAllByDataIds($ids);
                }

                if ($type == Req3Identifiers::TYPE_COMMUNICATION_CHANNELS) {
                    $valueIds = ArrayHelper::getColumn($values, 'value_id');
                    Req3TasksDataItemCommunicationChannels::deleteAll(['id' => $valueIds]);
                }
                if ($type == Req3Identifiers::TYPE_WH_BALANCE) {
                    Req3TasksDataItemWhBalance::deleteAll(['item_id' => $ids]);
                }
                /*if ($type == Req3Identifiers::TYPE_FILE_1) {
                    $valueIds = ArrayHelper::getColumn($values, 'value_id');
                    Req3TasksDataFiles::deleteAll(['id' => $valueIds]);
                }*/
                /*if ($type == Req3Identifiers::TYPE_COUNTERPARTIES_DOCUMENTS) {
                    $valueIds = ArrayHelper::getColumn($values, 'value_id');
                    Req3TasksDataItemDocs::deleteAll(['id' => $valueIds]);
                }*/
            }

            self::deleteAll(['id' => $dataIds]);
        }
    }


    // ============================================================================
    // ============================== ЧТО КАСАЕТСЯ OBJECT =========================
    // ============================================================================
    public function getRole()
    {
        return Yii::$app->authManager->getRole($this->value_text);
    }

    public function getAddressCoord()
    {
        $model = $this->getAddressModel();
        if ($model instanceof Users) $model = $model->address;
        if ($model instanceof MapAddresses) $model = $model->house;

        if ($model instanceof MapStreets) return $model->coord;
        if ($model instanceof MapHouses) return $model->coord;
        if ($model instanceof Locations) return $model->coord;
        return null;
    }

    public function getAddressDopData($type)
    {
        $address = $this->getAddressModel();
        if ($address instanceof Users) $address = $address->address;
        if ($address instanceof MapAddresses) {
            $item = MapAddressesDopData::getItem($address->dop_data, $type);
            return $item->data_text ?? null;
        }
        return null;
    }


    public function getAddressModel()
    {
        if (in_array($this->type, [Req3Identifiers::TYPE_ADDRESS, Req3Identifiers::TYPE_ADDRESS_ADD])) {
            return $this->address;
        }
        //-----------------------------------
        if ($this->type == Req3Identifiers::TYPE_ADDRESS_CAP) {
            return $this->cap->address ?? null;
        }
        //-----------------------------------
        if (in_array($this->type, [Req3Identifiers::TYPE_ADDRESS_ANY, Req3Identifiers::TYPE_ADDRESS_TREE_COVERAGE]) && $this->address_link) {
            return $this->address_link->getAddressModel();
        }
        //-----------------------------------
        if ($this->type == Req3Identifiers::TYPE_USER) {
            return $this->user;
        }
        //-----------------------------------
        if ($this->type == Req3Identifiers::TYPE_COUNTERPARTIES && $this->counterparty) {
            if ($this->counterparty->address) {
                return $this->counterparty->address;
            }
            foreach ($this->counterparty->link_users as $link_user) {
                if ($link_user->user) {
                    return $link_user->user;
                }
            }
        }
        //-----------------------------------
        if ($this->type == Req3Identifiers::TYPE_SWITCH_PORT) {
            $orderService = Yii::$container->get(OrderService::class);
            $houseId = $orderService->getHouseIdByDeviceId($this->value_id);
            if ($houseId) {
                return MapHouses::find()->id($houseId)->one();
            }
        }
        //-----------------------------------
        if ($this->type == Req3Identifiers::TYPE_KTV_DEVICE) {
            $ktvService = Yii::$container->get(KtvService::class);
            $device = $ktvService->getDeviceById($this->value_id);
            if ($device) {
                $orderService = Yii::$container->get(OrderService::class);
                $houseId = $orderService->getHouseIdByNodeId($device->usNodeId);
                if ($houseId) {
                    return MapHouses::find()->id($houseId)->one();
                }
            }
        }
        //-----------------------------------
        if ($this->type == Req3Identifiers::TYPE_ACS_DEVICE) {
            $acsService = Yii::$container->get(AcsService::class);
            $device = $acsService->getDeviceById($this->value_id);
            if ($device) {
                $houseIds = $acsService->getHouseIdsByDeviceId($device->id);
                if (!empty($houseIds)) {
                    return MapHouses::find()->id($houseIds)->one();//берем первый попавшийся дом ... а дальше будем думать ...
                }
            }
        }

        return null;
    }

    public function getAddressLocalityModel(): ?Locations
    {
        $address = $this->getAddressModel();
        if ($address) {
            if ($address instanceof Locations) {
                return $address;
            }
            if ($address instanceof MapStreets) {
                return $address->location;
            }
            if ($address instanceof MapHouses) {
                return $address->street->location ?? null;
            }
            if ($address instanceof MapAddresses) {
                return $address->house->street->location ?? null;
            }
            if ($address instanceof Users) {
                return $address->address->house->street->location ?? null;
            }
        }
        return null;
    }

    public function getAddressSetting($collapse = true, $phantomItems = [], $onlyThis = false): ?SettingDto
    {
        list($linkId, $linkType) = $this->getAddressSettingId();
        if ($linkId) {
            $settingService = Yii::$container->get(SettingService::class);
            return $settingService->getSettingByLinkId($linkType, $linkId, $collapse, $phantomItems, $onlyThis);
        }
        return null;
    }

    public function getAddressLocalitySetting($collapse = true, $phantomItems = [], $onlyThis = false): ?SettingDto
    {
        $address = $this->getAddressLocalityModel();
        if ($address) {
            $settingService = Yii::$container->get(SettingService::class);
            return $settingService->getSettingByLinkId(SettingLinkType::LOCATION, $address->id, $collapse, $phantomItems, $onlyThis);
        }
        return null;
    }

    public function getAddressSettingId()
    {
        $address = $this->getAddressModel();
        if ($address) {
            if ($address instanceof Locations) {
                return [$address->id, SettingLinkType::LOCATION];
            }
            if ($address instanceof MapStreets) {
                return [$address->street_id, SettingLinkType::STREET];
            }
            if ($address instanceof MapHouses) {
                return [$address->house_id, SettingLinkType::HOUSE];
            }
            if ($address instanceof MapAddresses) {
                return [$address->addr_id, SettingLinkType::FLAT];
            }
            if ($address instanceof Users) {
                return [$address->user_id, SettingLinkType::USER];
            }
        }
        return [null, null];
    }

    public function getAddressFullName($ignore_lvl = null, $invert = false, $separate_brackets = false)
    {
        if ($this->type == Req3Identifiers::TYPE_COUNTERPARTIES && $this->counterparty) {
            if ($this->counterparty->address) {
                return $this->counterparty->address->getFullName($ignore_lvl, $invert, $separate_brackets);
            }
            foreach ($this->counterparty->link_users as $link_user) {
                if ($link_user->user) {
                    return $link_user->user->getAddressFullName($ignore_lvl, $invert, $separate_brackets);
                }
            }
        }
        //-------------------------------------------
        $address = $this->getAddressModel();
        if ($address) {
            if ($address instanceof Locations) return $address->getFullName($ignore_lvl, $invert);
            if ($address instanceof MapStreets) return $address->getFullName($ignore_lvl, $invert, $separate_brackets);
            if ($address instanceof MapHouses) return $address->getFullName($ignore_lvl, $invert, $separate_brackets);
            if ($address instanceof MapAddresses) return $address->getFullName($ignore_lvl, $invert, $separate_brackets);
            if ($address instanceof Users) return $address->getAddressFullName($ignore_lvl, $invert, $separate_brackets);
        }
        //-----------------------------------
        return "";
    }

    public function getSimpleDataValues($add_detail_data = false, $ignore_details = [], $force_details = [])
    {
        $data = [
            'id'   => $this->identifier_id,
            'type' => $this->type,
            'name' => "type_{$this->type}_id{$this->identifier_id}",//заглушка, изменится на имя
            'value' => "!!! is not set up",//заглушка, изменится на нормализованное значение
            'link' => null,//заглушка, тут будет ссылка если имеется
            'items' => [],//если мультивыбор или вложенные данные то с такой же структурой детализация
        ];

        if ($this->identifier) {
            $data['name'] = $this->identifier->name;
        }

        if (in_array($this->type, [Req3Identifiers::TYPE_TEXT, Req3Identifiers::TYPE_TEXT_ADDRESS, Req3Identifiers::TYPE_TEXT_DATE, Req3Identifiers::TYPE_TEXT_DATE_TIME])) {
            $data['value'] = Str::compactOverflow($this->value_text, 1000);
        }

        if ($this->type == Req3Identifiers::TYPE_NUMBER) {
            $data['value'] = $this->value_number;
        }

        if ($this->type == Req3Identifiers::TYPE_TEXT_PHONE) {
            if ($this->phone) {
                $data['value'] = $this->phone->phone . " " . $this->phone->fio;
            }
        }

        if ($this->type == Req3Identifiers::TYPE_COMMUNICATION_CHANNELS) {
            if ($this->communication_channel_link) {
                $data['value'] = $this->communication_channel_link->getChannelTypeName() . ": " . $this->communication_channel_link->getSimpleValue();
            } else {
                $data['value'] = "communication_channel_link_{$this->value_id}";
            }
        }

        if ($this->type == Req3Identifiers::TYPE_USER) {
            $data['link'] = Url::toRoute(['/user1/profile/view', 'user_id' => $this->value_id], true);
            if ($this->user) {
                $data['value'] = "ЛС " . $this->user->utm_acc_id;
                if ($add_detail_data && $this->identifier) {
                    $data['items'] = $this->identifier->getSimpleDetailsValues($this, $ignore_details, $force_details);
                }
            } else {
                $data['value'] = "user_{$this->value_id}";
            }
        }

        if ($this->type == Req3Identifiers::TYPE_USER_TYPE_CONNECT) {
            $types = Users::CONN_TYPE_NAMES;
            if (isset($types[$this->value_id])) {
                $data['value'] = $types[$this->value_id];
            } else {
                $data['value'] = "type_{$this->value_id}";
            }
        }

        if ($this->type == Req3Identifiers::TYPE_USER_TYPE) {
            $types = Users::USER_TYPE_NAMES;
            if (isset($types[$this->value_id])) {
                $data['value'] = $types[$this->value_id];
            } else {
                $data['value'] = "type_{$this->value_id}";
            }
        }

        if ($this->type == Req3Identifiers::TYPE_COUNTERPARTIES_DIADOK_DOCUMENT_STATUS) {
            $types = CounterpartiesFileSetting::DIADOK_DOCUMENT_STATUSES;
            if (isset($types[$this->value_text])) {
                $data['value'] = $types[$this->value_text];
            } else {
                $data['value'] = "type_{$this->value_text}";
            }
        }

        if ($this->type == Req3Identifiers::TYPE_COUNTERPARTIES_DIADOK_DOCUMENT_TYPE) {
            $types = CounterpartiesFileSetting::DIADOK_DOCUMENT_TYPES;
            if (isset($types[$this->value_text])) {
                $data['value'] = $types[$this->value_text];
            } else {
                $data['value'] = "type_{$this->value_text}";
            }
        }

        if ($this->type == Req3Identifiers::TYPE_USER_BLOCK_REASON) {
            if ($this->block_reason) {
                $data['value'] = $this->block_reason->name;
            } else {
                $data['value'] = "block_reason_{$this->value_id}";
            }
        }

        if ($this->type == Req3Identifiers::TYPE_USER_CREDIT) {
            $types = UserCreditTypes::NAMES;
            if (isset($types[$this->value_id])) {
                $data['value'] = $types[$this->value_id];
            } else {
                $data['value'] = "user_credit_{$this->value_id}";
            }
        }

        if ($this->type == Req3Identifiers::TYPE_USER_KNOWN_FROM) {
            if ($this->known_from) {
                $data['value'] = $this->known_from->kf_text . (!empty($this->value_text) ? " ({$this->value_text})" : "");
            } else {
                $data['value'] = "user_known_from_{$this->value_id}";
            }
        }

        if ($this->type == Req3Identifiers::TYPE_USER_CONNECT_FROM) {
            $types = UserConnectionStatusTypes::NAMES;
            if (isset($types[$this->value_id])) {
                $data['value'] = $types[$this->value_id] . (!empty($this->value_text) ? " ({$this->value_text})" : "");
            } else {
                $data['value'] = "user_connect_from_{$this->value_id}";
            }
        }

        if (in_array($this->type, [Req3Identifiers::TYPE_ADDRESS, Req3Identifiers::TYPE_ADDRESS_ADD, Req3Identifiers::TYPE_ADDRESS_CAP, Req3Identifiers::TYPE_ADDRESS_ANY, Req3Identifiers::TYPE_ADDRESS_TREE_COVERAGE])) {
            $data['value'] = $this->getAddressFullName(Locations::TYPE_COUNTRY, true, true);
        }

        if ($this->type == Req3Identifiers::TYPE_ADDRESS_SETTING_LABEL) {
            $data['value'] = $this->label->label ?? "label_{$this->value_id}";
        }

        if ($this->type == Req3Identifiers::TYPE_FIN_MANAGER) {
            $data['value'] = $this->fm->fio ?? "fm_{$this->value_id}";
        }

        if ($this->type == Req3Identifiers::TYPE_UTM_TARIFF) {
            $data['value'] = $this->tariff->name ?? "utm_tariff_{$this->value_id}";
        }

        if ($this->type == Req3Identifiers::TYPE_UTM_SERVICE) {
            $data['value'] = $this->service->service_name ?? "utm_service_{$this->value_id}";
        }

        if ($this->type == Req3Identifiers::TYPE_UTM_DP) {
            $data['value'] = $this->dp->start_date ?? "utm_dp_{$this->value_id}";
        }

        if ($this->type == Req3Identifiers::TYPE_UTM_DP_MONTH_AUTO) {
            if ($this->value_id == -1) {
                $data['value'] = "Авто";
            } elseif ($this->dp) {
                $data['value'] = $this->dp->start_date;
            } else {
                $data['value'] = "utm_dp_month_auto_{$this->value_id}";
            }
        }

        if ($this->type == Req3Identifiers::TYPE_REWARD_SERVICE) {
            $data['value'] = $this->reward_service->name ?? "reward_service_{$this->value_id}";
        }

        if ($this->type == Req3Identifiers::TYPE_WH_ITEM) {
            $data['value'] = $this->item ? ($this->item->name . " ({$this->value_number})") : "wh_item_{$this->value_id}";
        }

        if ($this->type == Req3Identifiers::TYPE_WH_SYN_ITEM) {
            $data['value'] = $this->syn_item ? ($this->syn_item->name . " ({$this->value_number})") : "wh_syn_item_{$this->value_id}";
        }

        if ($this->type == Req3Identifiers::TYPE_WH_BALANCE) {
            $dataBalance = $this->balance ? $this->balance->generateCurrentBalance() : [];
            $data['value'] = ($dataBalance['type'] ?? "-") . ": " . ($dataBalance['value1'] ?? "-");
        }

        if ($this->type == Req3Identifiers::TYPE_WH_ITEM_SIMPLE) {
            $data['value'] = $this->item->name ?? "wh_item_simple_{$this->value_id}";
        }

        if ($this->type == Req3Identifiers::TYPE_WH_WAREHOUSE) {
            $data['value'] = $this->warehouse->name ?? "wh_warehouse_{$this->value_id}";
        }

        if ($this->type == Req3Identifiers::TYPE_WH_TEMPLATE) {
            $data['value'] = $this->wh_template->name ?? "wh_template_{$this->value_id}";
        }

        if ($this->type == Req3Identifiers::TYPE_OPER) {
            $data['value'] = $this->oper->fio ?? "oper_{$this->value_id}";
        }

        if ($this->type == Req3Identifiers::TYPE_OPER_ROLE) {
            $role = $this->getRole();
            $data['value'] = ($this->oper ? $this->oper->fio : "oper_{$this->value_id}") . " (" . ($role->description ?? "role_{$this->value_text}") . ")";
        }

        if ($this->type == Req3Identifiers::TYPE_FILE_1) {
            $data['value'] = $this->file->orig_name ?? "file_{$this->value_id}";
        }

        if ($this->type == Req3Identifiers::TYPE_LIST) {
            $data['value'] = $this->list_item->value ?? "list_{$this->value_id}";
        }

        if ($this->type == Req3Identifiers::TYPE_LIST_TREE) {
            $data['value'] = $this->list_tree_item->value ?? "list_tree_{$this->value_id}";
        }

        if ($this->type == Req3Identifiers::TYPE_ROLE) {
            $role = $this->getRole();
            $data['value'] = $role->description ?? "role_{$this->value_text}";
        }

        if ($this->type == Req3Identifiers::TYPE_GROUP) {
            $data['value'] = "";
            foreach ($this->children as $children) {
                $data_sub = $children->getSimpleDataValues($add_detail_data, $ignore_details, $force_details);
                $data['items'][] = $data_sub;
                $data['value'] .= "[{$data_sub['name']}: {$data_sub['value']}]";
            }
        }

        if ($this->type == Req3Identifiers::TYPE_COUNTERPARTIES) {
            $data['value'] = $this->counterparty ? $this->counterparty->getTitle() : "counterparty_{$this->value_id}";
            if ($add_detail_data && $this->identifier) {
                $data['items'] = $this->identifier->getSimpleDetailsValues($this, $ignore_details);
            }
        }

        if ($this->type == Req3Identifiers::TYPE_COUNTERPARTIES_DOCUMENTS) {
            $data['value'] = $this->doc_link ? $this->doc_link->getTitle() : "doc_link_{$this->value_id}";
        }

        if ($this->type == Req3Identifiers::TYPE_CALL_NAMES) {
            $data['value'] = $this->call_name->name ?? "call_name_{$this->value_id}";
        }

        if ($this->type == Req3Identifiers::TYPE_DOCUMENT_TYPE) {
            $data['value'] = $this->doc_type->name ?? "doc_type_{$this->value_id}";
        }

        if ($this->type == Req3Identifiers::TYPE_STATUS_ZONE) {
            $data['value'] = SettingStatusZone::getName($this->value_id);
        }

        if ($this->type == Req3Identifiers::TYPE_TEMPLATES) {
            $data['value'] = $this->template->name ?? "template_{$this->value_id}";
        }

        if ($this->type == Req3Identifiers::TYPE_TEMPLATE_STEPS) {
            $names = [];
            foreach ($this->template_steps as $template_step) {
                if ($template_step->version_id != null) {
                    $names[] = ($template_step->version && $template_step->version->template)
                        ? $template_step->version->template->name . " v" . $template_step->version->version
                        : "version_{$template_step->version_id}";
                } elseif ($template_step->step_id != null) {
                    $names[] = $template_step->step->name ?? "step_{$template_step->step_id}";
                } else {
                    $names[] = "[not selected]";
                }
            }
            if (count($names) > 0) {
                $data['value'] = implode(", ", $names);
            }
        }

        if ($this->type == Req3Identifiers::TYPE_VFP_LIST) {
            $data['value'] = $this->vfp->short_product ?? "vfp_{$this->value_id}";
        }

        if ($this->type == Req3Identifiers::TYPE_CHECK_WORK_RATER_CONFIRMATION) {
            $data['value'] = $this->work_rater_oper_confirmation->work_rater->name ?? "check_work_rater_confirmation_{$this->value_id}";
        }

        if ($this->type == Req3Identifiers::TYPE_CHECK_IDENTIFIER) {
            if ($this->value_number == self::VALUE_NUMBER_TYPE_CHECK_IDENTIFIER) {
                $data['value'] = $this->check_identifier->name ?? "check_identifier_{$this->value_id}";
            }
            if ($this->value_number == self::VALUE_NUMBER_TYPE_CHECK_WORK) {
                $data['value'] = $this->check_work->name ?? "check_work_{$this->value_id}";
            }
        }

        if ($this->type == Req3Identifiers::TYPE_CRASH) {
            $data['value'] = "#{$this->value_id}";
        }

        if ($this->type == Req3Identifiers::TYPE_CRASH_REASONS) {
            $data['value'] = $this->crashReason->name ?? "crash_reason_{$this->value_id}";
        }

        if ($this->type == Req3Identifiers::TYPE_SERVICE_BASKET) {
            $data['value'] = "";
            if ($this->basket) {
                $services = [];
                foreach ($this->basket->services as $service) {
                    $services[] = $service->reward_service->name ?? "-";
                }
                $data['value'] = implode(", ", $services);

                if ($this->basket->installment_type) {
                    $data['value'] .= "\nРассрочка на: {$this->basket->installment_value}";
                    if ($this->basket->installment_type == Req3TasksDataItemBaskets::INSTALLMENT_TYPE_MONTH_COUNT) {
                        $data['value'] .= "месяц.";
                    }
                    if ($this->basket->installment_type == Req3TasksDataItemBaskets::INSTALLMENT_TYPE_MONTHLY_AMOUNT) {
                        $data['value'] .= "р. в месяц.";
                    }
                }
            } else {
                $data['value'] = "Не привязана корзина";
            }
        }

        if ($this->type == Req3Identifiers::TYPE_REWARDS) {
            $data['value'] = $this->reward->work_rater->name ?? "-";
        }

        if ($this->type == Req3Identifiers::TYPE_QUEUE_LABEL) {
            $data['value'] = $this->queue_label->label ?? "-";
        }

        if ($this->type == Req3Identifiers::TYPE_BONUS_REWARD) {
            $data['value'] = $this->reward_bonus_item->name ?? "-";
        }

        if ($this->type == Req3Identifiers::TYPE_COUNTERPARTIES_ARCHIVE_DOC) {
            $data['value'] = ($this->archive_doc->setting->type->name ?? "-") .
                " (№" . ($this->archive_doc->setting->number ?? "-") .
                " от " . ($this->archive_doc->setting->date ?? "-") . ")";
        }
        if ($this->type == Req3Identifiers::TYPE_WORK_RATER) {
            $data['value'] = $this->workRater->name ?? "-";
        }
        if ($this->type == Req3Identifiers::TYPE_ORDER) {
            $data['value'] = "#$this->value_id";
        }
        if ($this->type == Req3Identifiers::TYPE_SWITCH_PORT) {
            $deviceService = Yii::$container->get(DeviceService::class);
            $device = $deviceService->getById($this->value_id);

            $text = "{$device->name} ({$device->location})";
            if (!empty($this->value_text)) $text .= " ($this->value_text)";

            $data['value'] = $text;
            $data['link'] = UserSideHelper::generateUsUrlDevice($this->value_id);
        }
        if ($this->type == Req3Identifiers::TYPE_KTV_DEVICE) {
            $ktvService = Yii::$container->get(KtvService::class);
            $device = $ktvService->getDeviceById($this->value_id);
            $data['value'] = $device->name ?? "#{$this->value_id}";
        }
        if ($this->type == Req3Identifiers::TYPE_ACS_DEVICE) {
            $acsService = Yii::$container->get(AcsService::class);
            $acsEnricher = Yii::$container->get(AcsHouseEnricher::class);
            $device = $acsService->getDeviceById($this->value_id);
            $acsEnricher->enrich($device);
            $data['value'] = $device ? $device->getName() : "#{$this->value_id}";
        }
        if ($this->type == Req3Identifiers::TYPE_ACS_INTERCOM_TYPE) {
            $data['value'] = IntercomType::getName($this->value_id);
        }

        return $data;
    }

    /**
     * @param Req3Tasks $task
     * @param           $to_identifier_id
     * @param null $parent_id
     * @param bool $is_partial_overwrite
     * @return Req3TasksDataItems
     * @throws Exception
     */
    public function copy(Req3Tasks $task, $to_identifier_id, $parent_id = null, $is_partial_overwrite = false, $old_data = [], $forceOperId = null)
    {
        $copy = new Req3TasksDataItems();
        $copy->load($this->attributes, '');
        $copy->parent_id = $parent_id;
        $copy->link_id = $task->id;
        $copy->link_type = Req3TasksDataItems::LINK_TYPE_TASK;
        $copy->identifier_id = $to_identifier_id;
        if ($forceOperId !== null) {
            $copy->oper_id = $forceOperId;
        }

        if ($is_partial_overwrite) {
            if ($copy->type == Req3Identifiers::TYPE_OPER_ROLE) {
                $old_map = ArrayHelper::map($old_data, 'value_id', 'value_number');
                if (isset($old_map[$copy->value_id])) {
                    $copy->value_number = $old_map[$copy->value_id];
                }
            }
        }

        if (in_array($copy->type, [Req3Identifiers::TYPE_ADDRESS_ANY, Req3Identifiers::TYPE_ADDRESS_TREE_COVERAGE]) && $this->address_link) {
            $model = new Req3TasksDataItemAddress();
            $model->load($this->address_link->attributes, '');
            $copy->populateRelation('address_link', $model);
        }

        if ($copy->type == Req3Identifiers::TYPE_TEMPLATE_STEPS) {
            $template_steps = [];
            foreach ($this->template_steps as $template_step) {
                $model = new Req3TasksDataItemSteps();
                $model->load($template_step->attributes, '');
                $template_steps[] = $model;
            }
            $copy->populateRelation('template_steps', $template_steps);
        }

        if ($copy->type == Req3Identifiers::TYPE_CHECK_IDENTIFIER) {
            $check_identifier_comments = [];
            foreach ($this->check_identifier_comments as $check_identifier_comment) {
                $model = new Req3TasksDataItemIdentifierComments();
                $model->load($check_identifier_comment->attributes, '');
                $check_identifier_comments[] = $model;
            }
            $copy->populateRelation('check_identifier_comments', $check_identifier_comments);
        }

        if ($copy->type == Req3Identifiers::TYPE_WH_BALANCE) {
            $balanceItems = [];
            foreach ($this->balanceItems as $balanceItem) {
                $model = new Req3TasksDataItemWhBalance();
                $model->load($balanceItem->attributes, '');
                $balanceItems[] = $model;
            }
            $copy->populateRelation('balanceItems', $balanceItems);
        }

        if ($copy->save(true, null, true)) {
            $simple_value = $copy->getSimpleDataValues();
            $task->addToHistoryChangeData($copy->identifier->name ?? $copy->identifier_id, [$simple_value['value']]);

            if ($parent_id == null) {
                $data = $task->data;
                $data[] = $copy;
                $task->onDataChange($data);
            }

            if ($this->type == Req3Identifiers::TYPE_GROUP) {

                $from_identifier = $this->identifier;
                $to_identifier = Req3Identifiers::find()->id($to_identifier_id)->one();
                //на всяк случай сверим
                if ($from_identifier && $to_identifier && $from_identifier->type == $to_identifier->type) {
                    /** @var Req3Identifiers[] $from_children */
                    $from_children = ArrayHelper::getColumn($from_identifier->identifier_links, 'link_identifier');
                    /** @var Req3Identifiers[] $to_children */
                    $to_children = ArrayHelper::getColumn($to_identifier->identifier_links, 'link_identifier');

                    $map = [];
                    foreach ($from_children as $from_key => $from) {
                        foreach ($to_children as $to_key => $to) {
                            if ($from->type == $to->type) {
                                $map[$from->id] = $to->id;
                                unset($to_children[$to_key]);
                                break;
                            }
                        }
                    }

                    $children = [];
                    foreach ($this->children as $child) {
                        if (isset($map[$child->identifier_id])) {
                            $children[] = $child->copy($task, $map[$child->identifier_id], $copy->id, false, [], $forceOperId);
                        }
                    }
                    $copy->populateRelation('children', $children);
                }
            }
        }

        return $copy;
    }

    public function setLink($link)
    {
        if ($link instanceof Req3Tasks) {
            $this->link_id = $link->id;
            $this->link_type = Req3TasksDataItems::LINK_TYPE_TASK;
            $this->populateRelation('task', $link);

            //---------------------
            //костыль, переносим в сотрудниках выбранные списки
            if ($this->type == Req3Identifiers::TYPE_OPER_ROLE) {
                foreach ($link->getDataIdentifier($this->identifier_id) as $old_item) {
                    if ($old_item->value_number !== null && $old_item->value_id == $this->value_id) {
                        $this->value_number = $old_item->value_number;
                    }
                }
            }
            //---------------------
        }
        if ($link instanceof Req3Scheduler) {
            $this->link_id = $link->id;
            $this->link_type = Req3TasksDataItems::LINK_TYPE_SCHEDULER;
            $this->populateRelation('scheduler', $link);
        }
    }

    /**
     * @param Req3Tasks $task
     * @param           $oper_id
     * @param array $only_check
     * @param array $errors
     * @return bool
     */
    public function isFill(Req3Tasks $task)
    {
        if ($this->identifier) {
            if ($this->identifier->type == Req3Identifiers::TYPE_GROUP) {
                $required_identifiers = ArrayHelper::map($this->identifier->identifier_links, 'link_identifier_id', 'link_identifier_id');
                foreach ($this->children as $data) {
                    if (in_array($data->identifier_id, $required_identifiers)) {
                        if (!$data->isFill($task)) {
                            return false;
                        } else {
                            unset($required_identifiers[$data->identifier_id]);
                        }
                    }
                }
                return count($required_identifiers) == 0;
            }

            if ($this->identifier->type == Req3Identifiers::TYPE_CHECK_WORK_RATER_CONFIRMATION) {
                if (!$this->work_rater_oper_confirmation) return false;

                if (!in_array($this->work_rater_oper_confirmation->status, [Req3WorkRaterOperConfirmation::STATUS_REJECTED, Req3WorkRaterOperConfirmation::STATUS_CREDITED, Req3WorkRaterOperConfirmation::STATUS_IMPOSSIBLE])) {
                    return false;
                }

                //решили что они мешают двигать задачу
//                $remarks = $this->work_rater_oper_confirmation->getRemarks();
//                if (count($remarks) > 0) {
//                    return false;
//                }
            }

            if ($this->identifier->type == Req3Identifiers::TYPE_CHECK_IDENTIFIER) {
                $need = $this->check_identifier_comments;
                foreach ($need as $k => $item) {
                    if (!in_array($item->status_execution, [Req3TasksDataItemIdentifierComments::STATUS_EXECUTION_PENDING_VERIFICATION, Req3TasksDataItemIdentifierComments::STATUS_EXECUTION_IN_WORK])) {
                        unset($need[$k]);
                    }
                }

                return count($need) == 0;
            }

            if ($this->identifier->type == Req3Identifiers::TYPE_OPER_ROLE) {
                $list = $this->identifier->type_info;
                if ($list !== null) {
                    if ($this->value_number === null) {
                        return false;
                    }
                }
            }

            if ($this->identifier->type == Req3Identifiers::TYPE_LIST_TREE) {
                $is_end_element = $this->identifier->getSettingByKey(Req3Identifiers::SETTING_END_ELEMENT, 0) == 1;
                if ($is_end_element) {
                    if (!$this->list_tree_item) return false;
                    foreach ($this->list_tree_item->children as $child) {
                        if ($child->is_approved == 1) return false;
                    }
                    return true;
                }
            }
            if ($this->identifier->type == Req3Identifiers::TYPE_SERVICE_BASKET) {
                if (empty($this->basket->services ?? [])) return false;
            }
        }

        return true;
    }

    public function isDataValueId()
    {
        return in_array($this->type, [
            Req3Identifiers::TYPE_TEXT_PHONE,
            Req3Identifiers::TYPE_COMMUNICATION_CHANNELS,
            Req3Identifiers::TYPE_FILE_1,
            Req3Identifiers::TYPE_USER,
            Req3Identifiers::TYPE_USER_TYPE_CONNECT,
            Req3Identifiers::TYPE_USER_TYPE,
            Req3Identifiers::TYPE_USER_BLOCK_REASON,
            Req3Identifiers::TYPE_USER_KNOWN_FROM,
            Req3Identifiers::TYPE_USER_CREDIT,
            Req3Identifiers::TYPE_USER_CONNECT_FROM,
            Req3Identifiers::TYPE_ADDRESS,
            Req3Identifiers::TYPE_ADDRESS_CAP,
            Req3Identifiers::TYPE_ADDRESS_ANY,
            Req3Identifiers::TYPE_ADDRESS_TREE_COVERAGE,
            Req3Identifiers::TYPE_ADDRESS_ADD,
            Req3Identifiers::TYPE_ADDRESS_SETTING_LABEL,
            Req3Identifiers::TYPE_FIN_MANAGER,
            Req3Identifiers::TYPE_UTM_TARIFF,
            Req3Identifiers::TYPE_UTM_SERVICE,
            Req3Identifiers::TYPE_UTM_DP,
            Req3Identifiers::TYPE_UTM_DP_MONTH_AUTO,
            Req3Identifiers::TYPE_REWARD_SERVICE,
            Req3Identifiers::TYPE_WH_ITEM,
            Req3Identifiers::TYPE_WH_SYN_ITEM,
            Req3Identifiers::TYPE_WH_ITEM_SIMPLE,
            Req3Identifiers::TYPE_WH_WAREHOUSE,
            Req3Identifiers::TYPE_WH_TEMPLATE,
            Req3Identifiers::TYPE_WH_BALANCE,
            Req3Identifiers::TYPE_OPER,
            Req3Identifiers::TYPE_OPER_ROLE,
            Req3Identifiers::TYPE_LIST,
            Req3Identifiers::TYPE_LIST_TREE,
            Req3Identifiers::TYPE_COUNTERPARTIES,
            Req3Identifiers::TYPE_COUNTERPARTIES_DOCUMENTS,
            Req3Identifiers::TYPE_CALL_NAMES,
            Req3Identifiers::TYPE_DOCUMENT_TYPE,
            Req3Identifiers::TYPE_STATUS_ZONE,
            Req3Identifiers::TYPE_TEMPLATES,
            Req3Identifiers::TYPE_CHECK_IDENTIFIER,
            Req3Identifiers::TYPE_CRASH,
            Req3Identifiers::TYPE_CRASH_REASONS,
            Req3Identifiers::TYPE_REWARDS,
            Req3Identifiers::TYPE_VFP_LIST,
            Req3Identifiers::TYPE_QUEUE_LABEL,
            Req3Identifiers::TYPE_BONUS_REWARD,
            Req3Identifiers::TYPE_COUNTERPARTIES_ARCHIVE_DOC,
            Req3Identifiers::TYPE_WORK_RATER,
            Req3Identifiers::TYPE_ORDER,
            Req3Identifiers::TYPE_SWITCH_PORT,
            Req3Identifiers::TYPE_KTV_DEVICE,
            Req3Identifiers::TYPE_ACS_DEVICE,
            Req3Identifiers::TYPE_ACS_INTERCOM_TYPE,
        ]);
    }

    public function isDataValueText()
    {
        if ($this->type == Req3Identifiers::TYPE_USER_KNOWN_FROM) {
            if ($this->known_from) {
                return $this->known_from->text_required == 1;
            }
            return false;
        }

        if ($this->type == Req3Identifiers::TYPE_USER_CONNECT_FROM) {
            if ($this->value_id == UserConnectionStatusTypes::RECONNECT) {
                return true;
            }
            return false;
        }

        return in_array($this->type, [
            Req3Identifiers::TYPE_TEXT,
            Req3Identifiers::TYPE_TEXT_ADDRESS,
            Req3Identifiers::TYPE_TEXT_DATE,
            Req3Identifiers::TYPE_TEXT_DATE_TIME,
            Req3Identifiers::TYPE_ROLE,
            Req3Identifiers::TYPE_OPER_ROLE,
            Req3Identifiers::TYPE_COUNTERPARTIES_DIADOK_DOCUMENT_STATUS,
            Req3Identifiers::TYPE_COUNTERPARTIES_DIADOK_DOCUMENT_TYPE,
        ]);
    }

    public function isDataValueNumber()
    {
        return in_array($this->type, [
            Req3Identifiers::TYPE_NUMBER
        ]);
    }

    public function isEmpty()
    {
        if ($this->isDataValueId() && empty($this->value_id)) return true;
        if ($this->isDataValueNumber() && ($this->value_number === null || strlen($this->value_number) == 0)) return true;
        if ($this->isDataValueText() && strlen(trim($this->value_text ?? "")) == 0) return true;

        if ($this->type == Req3Identifiers::TYPE_SERVICE_BASKET) {
            if (!$this->basket || count($this->basket->services) == 0) {
                return true;
            }
        }

        if ($this->type == Req3Identifiers::TYPE_TEMPLATE_STEPS) {
            if (count($this->template_steps) == 0) {
                return true;
            }
        }

        return false;
    }

    public function isAccessView($identification)
    {
        if ($this->link_type == self::LINK_TYPE_TASK && $this->task) {
            if ($this->task->isAccessView($identification)) {
                return true;
            }
        }
        return false;
    }

    public function isAccessLike($identification)
    {
        $oper = Opers::getOperByData($identification);
        if ($this->isAccessView($identification) || Yii::$app->authManager->checkAccess($oper->oper_id, "business.like")) return true;

        return false;
    }

    public function isWorkRaterConfirmed($oper_id)
    {
        if ($this->link_type == self::LINK_TYPE_TASK && $this->identifier) {
            $work_rated_confirmation = null;
            if (!empty($this->identifier->work_rated_id) && $this->identifier->work_rated) {
                $work_rated = $this->identifier->work_rated;
                $work_rated_qualification = Req3WorkRaterOperQualification::findOrCreate($oper_id, $work_rated->id);
                if ($work_rated_qualification->status == Req3WorkRaterOperQualification::STATUS_LEARNING) {
                    $work_rated_confirmation = Req3WorkRaterOperConfirmation::findOrCreateByType($oper_id, $work_rated->id, $this->link_id, Req3WorkRaterOperConfirmation::LINK_TYPE_TASK, $this->deferred_key, $this->identifier_id);
                }
            }
            if ($work_rated_confirmation && $work_rated_confirmation->status == Req3WorkRaterOperConfirmation::STATUS_NOT_CONFIRMED) {
                return false;
            }
        }
        return true;
    }
    // ============================================================================
    // ============================== STATIC ======================================
    // ============================================================================
    /**
     * @param                         $data
     * @param null  $link
     * @param null  $oper_id
     * @param array $errors
     * @param bool  $full_error
     * @param false $skip_empty
     * @param bool  $is_test_not_save
     * @return Req3TasksDataItems[]
     * @throws InvalidConfigException
     */
    public static function loadCreateObject($data, $link = null, $oper_id = null, &$errors = [], $full_error = false, $skip_empty = false, $is_test_not_save = false)
    {
        //------------------------------------------------
        //перебираем мало-ли там мульти ввод, разбиваем на разные объекты
        $data_values = [];
        foreach ($data as $data_value) {
            if ($data_value instanceof Req3TasksDataItems) {
                $data_values[] = $data_value;
            } else {
                $array_check_column = ['value_id', 'value_text', 'value_number'];

                $key_array = null;
                foreach ($array_check_column as $column) {
                    if (isset($data_value[$column]) && is_array($data_value[$column])) {
                        $key_array = $column;
                    }
                }

                if ($key_array === null) {
                    $data_values[] = $data_value;
                } else {
                    foreach ($data_value[$key_array] as $value) {
                        $clone_data_value = $data_value;
                        $clone_data_value[$key_array] = $value;
                        $data_values[] = $clone_data_value;
                    }
                }
            }
        }
        //------------------------------------------------
        $fm_id = null;
        if ($link instanceof Req3Tasks) $fm_id = $link->fm_id;
        if ($link instanceof Req3Scheduler) $fm_id = $link->fm_id;

        foreach ($data_values as $key => $data_value) {
            $role = null;
            if (!($data_value instanceof Req3TasksDataItems)) {
                $role = $data_value['value_text'] ?? "";
                if (!empty($role)) {
                    $identifier = Req3Identifiers::find()->id($data_value['identifier_id'])->one();
                    if ($identifier && $identifier->type == Req3Identifiers::TYPE_OPER_ROLE) {
                        $is_hide_oper = $identifier->getSettingByKey(Req3Identifiers::SETTING_OPER_ROLE_HIDE_OPER);
                        if ($is_hide_oper) {
                            unset($data_values[$key]);
                            $opers = (new Query())->from(['t' => Opers::find()
                                ->select([RbacItem::tableName() . '.name', Opers::tableName() . '.oper_id'])
                                ->andWhere([RbacItem::tableName() . '.name' => $role])
                                ->joinWith(['rbac_assignments.item'], false)
                                ->andWhere([RbacItem::tableName() . '.type' => \yii\rbac\Item::TYPE_ROLE])
                                ->groupBy([RbacItem::tableName() . '.name', Opers::tableName() . '.oper_id'])
                                ->asArray()])->all();

                            foreach ($opers as $oper) {
                                if ($fm_id == null || OperRoleFmLink::isAccessOperRoleFm($oper['oper_id'], $oper['name'], $fm_id)) {
                                    $clone_data_value = $data_value;
                                    $clone_data_value['value_id'] = $oper['oper_id'];
                                    $data_values[] = $clone_data_value;
                                }
                            }
                        }
                    }
                }
            }
        }

        //------------------------------------------------
        // преобразуем в объекты
        /** @var Req3TasksDataItems[] $task_data */
        $task_data = [];
        foreach ($data_values as $i => $data_value) {
            if ($data_value instanceof Req3TasksDataItems) {
                $task_data[$i] = $data_value;
            } else {
                $value = new Req3TasksDataItems();
                $value->setLink($link);
                $value->oper_id = $oper_id;
                $value->date_add = new Expression("NOW()");
                $value->load($data_value, '', $oper_id, $is_test_not_save);

                if (!$skip_empty || !$value->isEmpty()) {
                    if ($value->validate(['value_id', 'value_text', 'value_number'], false)) {
                        $task_data[$i] = $value;
                    } else {
                        foreach ($value->getErrors() as $_errors) {
                            foreach ($_errors as $_error) {
                                $errors[] = ($full_error ? ("«" . ($value->identifier->name ?? "identifier{$value->identifier_id}") . "»: ") : "") . $_error;
                            }
                        }
                    }
                }
                if (isset($data_value['children'])) {
                    $value->populateRelation('children', self::loadCreateObject($data_value['children'], $link, $oper_id, $errors, true, $skip_empty, $is_test_not_save));
                }
            }
        }
        //------------------------------------------------
        return $task_data;
    }

    /**
     * @param self[] $data
     * @param array $setting
     */
    public static function withTypes($data, $setting)
    {
        $data = ArrayHelper::map($data, 'id', function ($item) {
            return $item;
        }, 'type');
        foreach ($setting as $type => $with) {
            if (isset($data[$type])) {
                ModelHelper::loadWith($data[$type], $with);
            }
        }
    }

    public static function getWidgetValueData($identifier, $values, $fnct_name = null, $fnct_id = null, $forced_array = false)
    {
        $fnct_get_value = function (Req3TasksDataItems $value) use (&$fnct_id) {
            if ($fnct_id != null) {
                return $fnct_id($value);
            } else {
                return $value->value_id;
            }
        };

        if (!$identifier->isMultiSimple()) {
            $widget_value = null;
            $widget_text = null;
            if ($forced_array) {
                $widget_value = [];
                $widget_text = [];
            }
            if (count($values) > 0) {
                $key = array_key_first($values);
                if ($forced_array) $widget_value[] = $fnct_get_value($values[$key]);
                else $widget_value = $fnct_get_value($values[$key]);

                if ($fnct_name != null) {
                    if ($forced_array) $widget_text[] = $fnct_name($values[$key]);
                    else $widget_text = $fnct_name($values[$key]);
                }
            }
            return [$widget_value, $widget_text];
        } else {
            $widget_value = [];
            $widget_text = [];
            foreach ($values as $value) {
                $widget_value[] = $fnct_get_value($value);
                if ($fnct_name != null) {
                    $widget_text[] = $fnct_name($value);
                }
            }
        }
        return [$widget_value, $widget_text];
    }

    /**
     * @param Req3Identifiers $identifier
     * @param Req3TasksDataItems[] $data
     * @param Req3Tasks       $task
     * @return bool
     */
    public static function isFillData(Req3Identifiers $identifier, $data, $task)
    {
        $map_data = self::getMapData($data);
        if ($identifier->type == Req3Identifiers::TYPE_CALL_STATUS) {
            if (count($task->calls_statuses_step) == 0) {
                return false;
            }
        } elseif ($identifier->type == Req3Identifiers::TYPE_GHOST) {
            return self::isFillData($identifier->identifier_link, $data, $task);
        } elseif (!isset($map_data[$identifier->id])) {
            return false;
        } else {
            foreach ($map_data[$identifier->id] as $data) {
                if (!$data->isFill($task)) {
                    return false;
                }
            }

            if ($identifier->type == Req3Identifiers::TYPE_LIST) {
                $is_checklist = $identifier->getSettingByKey(Req3Identifiers::SETTING_IS_CHECKLIST, 0);
                $select_all = $identifier->getSettingByKey(Req3Identifiers::SETTING_NEED_SELECT_ALL, $is_checklist);
                if ($is_checklist == 1 && $select_all == 1) {
                    $list_group = $identifier->list_group;
                    if (!$list_group) return false;
                    if ($identifier->is_multi) {
                        return count($identifier->list_group->items) <= count($map_data[$identifier->id]);
                    }
                }
            }
        }
        return true;
    }

    /**
     * @param Req3TasksDataItems[] $data
     * @return Req3TasksDataItems[][]
     */
    public static function getMapData($data)
    {
        /** @var Req3TasksDataItems[][] $map_data */
        $map_data = ArrayHelper::map($data, 'id', fn($item) => $item, 'identifier_id');
        return $map_data;
    }
}
