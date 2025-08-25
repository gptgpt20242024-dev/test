<?php

namespace app\modules\process\models\identifiers;

use app\components\Str;
use app\models\RegexpItems;
use app\modules\lists\models\ListsGroups;
use app\modules\lists\models\ListsTreeGroups;
use app\modules\process\components\HelperReSave;
use app\modules\process\dto\data\TaskDataDto;
use app\modules\process\dto\RuleDataDto;
use app\modules\process\models\_query;
use app\modules\process\models\Req3Setting;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItems;
use app\modules\process\models\template\Req3Templates;
use app\modules\process\models\template\Req3TemplateVersions;
use app\modules\process\models\template_steps\Req3TemplateStepRoles;
use app\modules\process\models\template_steps\Req3TemplateSteps;
use app\modules\process\models\work_raters\Req3WorkRaters;
use app\modules\process\services\ProcessTaskService;
use Exception;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "req3_identifiers".
 *
 * @property integer                 $id
 * @property integer                 $template_id
 * @property integer                 $version_id
 * @property string                  $name
 * @property string                  $description
 * @property string                  $identifier
 * @property integer                 $type
 * @property integer                 $type_info
 * @property integer                 $is_multi
 * @property string                  $setting_json
 * @property integer                 $work_rated_id
 *
 * @property Req3Templates           $template
 * @property Req3TemplateVersions    $version
 * @property Req3IdentifierLinks[]   $identifier_links
 * @property Req3IdentifierLinks[]   $parent_links
 * @property Req3Identifiers[]       $identifiers
 * @property ListsGroups             $list_group
 * @property ListsTreeGroups         $list_tree_group
 * @property RegexpItems             $regexp
 * @property Req3IdentifierDetails[] $details
 * @property Req3WorkRaters          $work_rated
 * @property Req3Identifiers         $identifier_link
 */
class Req3Identifiers extends ActiveRecord
{
    // ============================================================================
    // ============================== КОНСТАНТЫ ===================================
    // ============================================================================
    //1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 21, 22
    //30
    //41
    //50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64
    //71, 72, 73, 74, 75, 76, 77
    //90
    //100 .. 120 .. 200, 201
    //501 .. 510 .. 515 .. 520 .. 530 .. 540 .. 620, 621 .. 999
    //5001 .. 5005 .. 5010 .. 5020 .. 5030 .. 5040 .. 5400 .. 5600 .. 5610, 5611

    const TYPE_NUMBER                                = 1;
    const TYPE_TEXT                                  = 2;
    const TYPE_TEXT_ADDRESS                          = 22;
    const TYPE_TEXT_DATE                             = 4;
    const TYPE_TEXT_DATE_TIME                        = 41;
    const TYPE_TEXT_PHONE                            = 3;
    const TYPE_COMMUNICATION_CHANNELS                = 30;
    const TYPE_FILE_1                                = 5;
    const TYPE_DOCUMENT_TYPE                         = 501;
    const TYPE_LIST                                  = 6;
    const TYPE_LIST_TREE                             = 11;
    const TYPE_USER                                  = 50;
    const TYPE_USER_TYPE_CONNECT                     = 5001;
    const TYPE_USER_KNOWN_FROM                       = 5005;
    const TYPE_USER_CREDIT                           = 5010;
    const TYPE_USER_CONNECT_FROM                     = 5020;
    const TYPE_USER_TYPE                             = 5030;
    const TYPE_USER_BLOCK_REASON                     = 5040;
    const TYPE_ADDRESS                               = 51;
    const TYPE_ADDRESS_ANY                           = 510;
    const TYPE_ADDRESS_ADD                           = 515;
    const TYPE_ADDRESS_TREE_COVERAGE                 = 520;
    const TYPE_ADDRESS_CAP                           = 530;
    const TYPE_ADDRESS_SETTING_LABEL                 = 5400;
    const TYPE_FIN_MANAGER                           = 52;
    const TYPE_OPER                                  = 53;
    const TYPE_OPER_ROLE                             = 540;
    const TYPE_GHOST                                 = 13;
    const TYPE_ROLE                                  = 54;
    const TYPE_COUNTERPARTIES                        = 55;
    const TYPE_COUNTERPARTIES_DOCUMENTS              = 5600;
    const TYPE_COUNTERPARTIES_DIADOK_DOCUMENT_STATUS = 5610;
    const TYPE_COUNTERPARTIES_DIADOK_DOCUMENT_TYPE   = 5611;
    const TYPE_COUNTERPARTIES_ARCHIVE_DOC            = 18;
    const TYPE_UTM_TARIFF                            = 60;
    const TYPE_UTM_SERVICE                           = 61;
    const TYPE_UTM_DP                                = 62;
    const TYPE_UTM_DP_MONTH_AUTO                     = 620;
    const TYPE_REWARD_SERVICE                        = 621;
    const TYPE_WH_ITEM                               = 71;
    const TYPE_WH_ITEM_SIMPLE                        = 72;
    const TYPE_WH_WAREHOUSE                          = 73;
    const TYPE_WH_TEMPLATE                           = 74;
    const TYPE_WH_SYN_ITEM                           = 75;
    const TYPE_WH_BALANCE                            = 19;
    const TYPE_GROUP                                 = 999;
    const TYPE_CALL_STATUS                           = 100;
    const TYPE_CALL_NAMES                            = 120;
    const TYPE_STATUS_ZONE                           = 90;
    const TYPE_TEMPLATES                             = 200;
    const TYPE_TEMPLATE_STEPS                        = 201;
    const TYPE_QUEUE_LABEL                           = 14;
    const TYPE_BONUS_REWARD                          = 17;
    const TYPE_VFP_LIST                              = 9;
    const TYPE_SERVICE_BASKET                        = 10;
    const TYPE_CHECK_WORK_RATER_CONFIRMATION         = 15;
    const TYPE_CHECK_IDENTIFIER                      = 16;
    const TYPE_CRASH                                 = 7;
    const TYPE_CRASH_REASONS                         = 58;
    const TYPE_REWARDS                               = 8;
    const TYPE_PROJECT_TREE                          = 12;
    const TYPE_WORK_RATER                            = 21;
    const TYPE_ORDER                                 = 59;
    const TYPE_SWITCH_PORT                           = 63;
    const TYPE_KTV_DEVICE                            = 64;
    const TYPE_ACS_DEVICE                            = 76;
    const TYPE_ACS_INTERCOM_TYPE                     = 77;

    const TYPE_TEMP_EXECUTOR   = 10000;
    const TYPE_TEMP_CONTROLLER = 10001;
    const TYPE_TEMP_OBSERVER   = 10002;

    const TYPES = [
        self::TYPE_NUMBER                                => "Число",
        self::TYPE_TEXT                                  => "Текст",
        self::TYPE_TEXT_ADDRESS                          => "Текст (подсказка адреса)",
        self::TYPE_TEXT_DATE                             => "Дата",
        self::TYPE_TEXT_DATE_TIME                        => "Дата + время",
        self::TYPE_TEXT_PHONE                            => "Телефон",
        self::TYPE_COMMUNICATION_CHANNELS                => "Канал связи",
        self::TYPE_FILE_1                                => "Файл",
        self::TYPE_DOCUMENT_TYPE                         => "Тип документа",
        self::TYPE_LIST                                  => "Список",
        self::TYPE_LIST_TREE                             => "Вложенный список",
        self::TYPE_USER                                  => "Абонент",
        self::TYPE_USER_TYPE_CONNECT                     => "Тип подключения",
        self::TYPE_USER_KNOWN_FROM                       => "Откуда узнал",
        self::TYPE_USER_CREDIT                           => "Рассрочка",
        self::TYPE_USER_CONNECT_FROM                     => "Статус подключения",
        self::TYPE_USER_TYPE                             => "Тип Абонента",
        self::TYPE_USER_BLOCK_REASON                     => "Причина блокировки",
        self::TYPE_ADDRESS                               => "Адрес (дом/квартира)",
        self::TYPE_ADDRESS_ADD                           => "Адрес c возможностью добавления",
        self::TYPE_ADDRESS_ANY                           => "Адрес (любая сущность)",
        self::TYPE_ADDRESS_TREE_COVERAGE                 => "Адрес (дерево проникновения до улицы)",
        self::TYPE_ADDRESS_CAP                           => "Адрес (ТКД)",
        self::TYPE_ADDRESS_SETTING_LABEL                 => "Метки (Настройки адреса)",
        self::TYPE_FIN_MANAGER                           => "Фин. менеджер",
        self::TYPE_OPER                                  => "Исполнитель",
        self::TYPE_OPER_ROLE                             => "Исполнитель + роль",
        self::TYPE_ROLE                                  => "Роль",
        self::TYPE_COUNTERPARTIES                        => "Контрагент",
        self::TYPE_COUNTERPARTIES_DOCUMENTS              => "Документ контрагента",
        self::TYPE_COUNTERPARTIES_DIADOK_DOCUMENT_STATUS => "Статус Документа ДИАДОК",
        self::TYPE_COUNTERPARTIES_DIADOK_DOCUMENT_TYPE   => "Тип Документа ДИАДОК",
        self::TYPE_UTM_TARIFF                            => "Тариф",
        self::TYPE_UTM_SERVICE                           => "Услуга",
        self::TYPE_UTM_DP                                => "Расчетный период",
        self::TYPE_UTM_DP_MONTH_AUTO                     => "Расчетный период (месячные + авто)",
        self::TYPE_REWARD_SERVICE                        => "Услуги БП",
        self::TYPE_WH_ITEM                               => "Наименования склада (c количеством)",
        self::TYPE_WH_ITEM_SIMPLE                        => "Наименования склада",
        self::TYPE_WH_WAREHOUSE                          => "Склад",
        self::TYPE_WH_SYN_ITEM                           => "Материал",
        self::TYPE_WH_TEMPLATE                           => "Шаблон списания",
        self::TYPE_WH_BALANCE                            => "Баланс склада",
        self::TYPE_GROUP                                 => "Группа идентификаторов",
        self::TYPE_CALL_STATUS                           => "Статус обзвонки",
        self::TYPE_CALL_NAMES                            => "Название линии",
        self::TYPE_STATUS_ZONE                           => "Статус зоны покрытия",
        self::TYPE_TEMPLATES                             => "Шаблоны БП",
        self::TYPE_TEMPLATE_STEPS                        => "Шаблоны/Шаги БП",
        self::TYPE_VFP_LIST                              => "ЦКП",
        self::TYPE_SERVICE_BASKET                        => "Корзина услуг",
        self::TYPE_CHECK_WORK_RATER_CONFIRMATION         => "Проверка обучения",
        self::TYPE_CHECK_IDENTIFIER                      => "Проверка идентификатора",
        self::TYPE_CRASH                                 => "Авария",
        self::TYPE_CRASH_REASONS                         => "Причины аварии",
        self::TYPE_REWARDS                               => "Начисления",
        self::TYPE_PROJECT_TREE                          => "Дерево проектов",
        self::TYPE_GHOST                                 => "Обёртка",
        self::TYPE_QUEUE_LABEL                           => "Метка очереди",
        self::TYPE_BONUS_REWARD                          => "Штраф/бонус",
        self::TYPE_COUNTERPARTIES_ARCHIVE_DOC            => "Документ в архиве",
        self::TYPE_WORK_RATER                            => "Работа",
        self::TYPE_ORDER                                 => "Наряд",
        self::TYPE_SWITCH_PORT                           => "Устройство (US)",
        self::TYPE_KTV_DEVICE                            => "Устройство (KTV)",
        self::TYPE_ACS_DEVICE                            => "Устройство (СКУД)",
        self::TYPE_ACS_INTERCOM_TYPE                     => "Система домофона (СКУД)",
    ];

    const TYPES_COMPATIBLE = [
        [
            self::TYPE_WH_ITEM,
            self::TYPE_WH_ITEM_SIMPLE,
        ],
        [
            self::TYPE_TEXT,
            self::TYPE_TEXT_ADDRESS,
            self::TYPE_TEXT_DATE,
            self::TYPE_TEXT_DATE_TIME,
            self::TYPE_ROLE
        ],
        [
            self::TYPE_OPER_ROLE,
            self::TYPE_OPER,
            self::TYPE_ROLE,//получается дилема что исполнителя могут изменить на роль, и тогда будет пустота ...
        ],
        [
            self::TYPE_ADDRESS,
            self::TYPE_ADDRESS_ADD
        ]
    ];

    //список элементов для мультиввода которых не нужен сложный блок
    const TYPES_MULTI_SIMPLE = [
        self::TYPE_LIST,
        self::TYPE_LIST_TREE,
        self::TYPE_USER,
        self::TYPE_USER_TYPE_CONNECT,
        self::TYPE_USER_CREDIT,
        self::TYPE_USER_TYPE,
        self::TYPE_COUNTERPARTIES_DIADOK_DOCUMENT_STATUS,
        self::TYPE_COUNTERPARTIES_DIADOK_DOCUMENT_TYPE,
        self::TYPE_USER_BLOCK_REASON,
        self::TYPE_ADDRESS,
        self::TYPE_ADDRESS_CAP,
        self::TYPE_ADDRESS_ANY,
        self::TYPE_ADDRESS_TREE_COVERAGE,
        self::TYPE_FIN_MANAGER,
        self::TYPE_OPER,
        self::TYPE_OPER_ROLE,
        self::TYPE_ROLE,
        self::TYPE_COUNTERPARTIES,
        self::TYPE_UTM_TARIFF,
        self::TYPE_UTM_SERVICE,
        self::TYPE_UTM_DP,
        self::TYPE_UTM_DP_MONTH_AUTO,
        self::TYPE_REWARD_SERVICE,
        self::TYPE_WH_ITEM_SIMPLE,
        self::TYPE_WH_WAREHOUSE,
        self::TYPE_WH_SYN_ITEM,
        self::TYPE_WH_TEMPLATE,
        self::TYPE_CALL_NAMES,
        self::TYPE_STATUS_ZONE,
        self::TYPE_TEMPLATES,
        self::TYPE_VFP_LIST,
        self::TYPE_DOCUMENT_TYPE,
        self::TYPE_REWARDS,
        self::TYPE_QUEUE_LABEL,
        self::TYPE_BONUS_REWARD,
        self::TYPE_WORK_RATER,
        self::TYPE_CRASH_REASONS,
    ];

    //список элементов для которых запрещен мультиввод
    const TYPES_FORBIDDEN_MULTI = [
        self::TYPE_CALL_STATUS,
        self::TYPE_SERVICE_BASKET,
        self::TYPE_LIST_TREE,
        self::TYPE_PROJECT_TREE,
        self::TYPE_GHOST,
    ];

    //список элементов для которых запрещено редактирование (например автоматическое заполнение)
    const TYPES_FORBIDDEN_EDIT = [
        self::TYPE_CALL_STATUS,
        self::TYPE_COUNTERPARTIES_ARCHIVE_DOC,
    ];

    //список элементов для которых не выводить карандаш редактирование, данные будут вводится другим способом (например чекбокс лист)
    protected const TYPES_CUSTOM_EDIT = [
        self::TYPE_LIST,
        self::TYPE_LIST_TREE,
        self::TYPE_CHECK_WORK_RATER_CONFIRMATION,
        self::TYPE_CHECK_IDENTIFIER,
        self::TYPE_SERVICE_BASKET,
        self::TYPE_REWARDS,
        self::TYPE_PROJECT_TREE,
        self::TYPE_GHOST,
    ];

    //список элементов для которых одна xml для вывода всех элементов ($values break;)
    protected const TYPES_CUSTOM_VIEW = [
        self::TYPE_LIST,
        self::TYPE_LIST_TREE,
        self::TYPE_COMMUNICATION_CHANNELS,
        self::TYPE_REWARDS,
        self::TYPE_GHOST
    ];

    //список элементов для формулы приоритета
    const TYPES_FORMULA_PRIORITY = [
        Req3Identifiers::TYPE_NUMBER,
        Req3Identifiers::TYPE_USER,
        Req3Identifiers::TYPE_UTM_TARIFF,
        Req3Identifiers::TYPE_UTM_SERVICE,
        Req3Identifiers::TYPE_TEMPLATE_STEPS,
        Req3Identifiers::TYPE_LIST_TREE,
        Req3Identifiers::TYPE_LIST,
        Req3Identifiers::TYPE_PROJECT_TREE,
        Req3Identifiers::TYPE_ADDRESS,
        Req3Identifiers::TYPE_ADDRESS_ADD,
        Req3Identifiers::TYPE_ADDRESS_CAP,
        Req3Identifiers::TYPE_ADDRESS_ANY,
        Req3Identifiers::TYPE_ADDRESS_TREE_COVERAGE,
        Req3Identifiers::TYPE_ORDER,
    ];

    public const SETTING_IS_CHECKLIST    = "is_checklist";
    public const SETTING_IS_SHOW_LINES   = "is_show_lines";
    public const SETTING_IS_ONE_LINE     = "is_one_line";
    public const SETTING_NON_BLOCK       = "non_block";
    public const SETTING_NEED_SELECT_ALL = "select_all";
    public const SETTING_END_ELEMENT     = "end_element";

    public const SETTING_WH_NEED_ITEMS = "wh_need_items";
    public const SETTING_WH_SYN_IDS    = "syn_ids";

    public const SETTING_WH_WAREHOUSE_FILTER_FM = "filter_fm";

    public const SETTING_SELECT_LIST_ONLY_MY        = "select_list_only_my";
    public const SETTING_OPER_LIST_ITEMS_IGNORE     = "oper_list_items_ignore";
    public const SETTING_OPER_ROLE_HIDE_OPER        = "oper_role_hide_oper";
    public const SETTING_OPER_ROLE_REQUIRED_COMMENT = "oper_role_required_comment";

    public const SETTING_NO_EXPORT = "no_export";

    public const SETTING_SPOILER = "spoiler";

    public const SETTING_ADDRESS_PROCESS = "process";
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
        return 'req3_identifiers';
    }

    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'template_id'   => 'Процесс',
            'version_id'    => 'Version ID',
            'name'          => 'Название',
            'description'   => 'Описание',
            'identifier'    => 'Идентификатор',
            'type'          => 'Тип',
            'type_info'     => 'Type Info',
            'is_multi'      => 'Is Multi',
            'setting_json'  => 'Setting Json',
            'work_rated_id' => 'Работа',
        ];
    }

    public static function find()
    {
        return new _query\Req3IdentifiersQuery(get_called_class());
    }

    // ============================================================================
    // ============================== ПРАВИЛА =====================================
    // ============================================================================

    public function rules()
    {
        return [
            [['template_id', 'version_id', 'type', 'type_info', 'is_multi', 'work_rated_id'], 'integer'],
            [['name', 'identifier', 'type'], 'required'],
            ['type_info', 'required', 'when' => function ($model) {
                return array_search($model->type, [Req3Identifiers::TYPE_LIST, Req3Identifiers::TYPE_LIST_TREE]) !== false;
            }],
            [['description', 'setting_json'], 'string'],
            [['name', 'identifier'], 'string', 'max' => 255],
            [['identifier'], 'validateIdentifierUnique'],
        ];
    }

    public function validateIdentifierUnique($attribute)
    {
        $q_identifier = Req3Identifiers::find()->identifier($this->identifier)->andFilterWhere(['!=', 'id', $this->id]);
        if ($this->version_id !== null) {
            $q_identifier->versionId($this->version_id, true);
        }
        if ($q_identifier->exists()) {
            $this->addError($attribute, 'Такой идентификатор уже занят');
        }
    }

    public function load($data, $formName = null)
    {
        $scope = $formName === null ? $this->formName() : $formName;


        $identifier_links = [];
        if (isset($data[$scope]) && isset($data[$scope]['link_identifier_id'])) {
            foreach ($data[$scope]['link_identifier_id'] as $link_identifier_id) {
                $model = new Req3IdentifierLinks();
                $model->link_identifier_id = $link_identifier_id;
                $identifier_links[] = $model;
            }
        }
        $this->populateRelation('identifier_links', $identifier_links);


        //загружаем доп данные отображаемых данных
        $details = [];
        if (isset($data[$scope]) && isset($data[$scope]['details'])) {
            foreach ($data[$scope]['details'] as $type) {
                $detail = new Req3IdentifierDetails();
                $detail->type = $type;
                $details[] = $detail;
            }
        }
        $this->populateRelation('details', $details);

        //доп настройки
        if (isset($data[$scope]) && isset($data[$scope]['setting'])) {
            $this->setting_json = json_encode($data[$scope]['setting']);
        }

        return parent::load($data, $formName);
    }

    // ============================================================================
    // ============================== СЦЕНАРИИ ====================================
    // ============================================================================

    // ============================================================================
    // ============================== ГЕТТЕРЫ =====================================
    // ============================================================================
    public function getIdentifier_links()
    {
        return $this->hasMany(Req3IdentifierLinks::class, ['identifier_id' => 'id']);
    }

    public function getParent_links()
    {
        return $this->hasMany(Req3IdentifierLinks::class, ['link_identifier_id' => 'id']);
    }

    public function getIdentifiers()
    {
        return $this->hasMany(Req3Identifiers::class, ['id' => 'link_identifier_id'])
            ->via('identifier_links');
    }

    public function getTemplate()
    {
        return $this->hasOne(Req3Templates::class, ['id' => 'template_id']);
    }

    public function getVersion()
    {
        return $this->hasOne(Req3TemplateVersions::class, ['id' => 'version_id']);
    }

    public function getList_group()
    {
        return $this->hasOne(ListsGroups::class, ['id' => 'type_info']);
    }

    public function getList_tree_group()
    {
        return $this->hasOne(ListsTreeGroups::class, ['id' => 'type_info']);
    }

    public function getRegexp()
    {
        return $this->hasOne(RegexpItems::class, ['id' => 'type_info']);
    }

    public function getDetails()
    {
        return $this->hasMany(Req3IdentifierDetails::class, ['identifier_id' => 'id']);
    }

    public function getWork_rated()
    {
        return $this->hasOne(Req3WorkRaters::class, ['id' => 'work_rated_id']);
    }

    public function getIdentifier_link()
    {
        return $this->hasOne(Req3Identifiers::class, ['id' => 'type_info']);
    }

    // ============================================================================
    // ============================== СЕТТЕРЫ =====================================
    // ============================================================================

    // ============================================================================
    // ============================== СОБЫТИЯ СОХРАНЕНИЯ ==========================
    // ============================================================================
    public function save($runValidation = true, $attributeNames = null, $cascade = false)
    {
        if (parent::save($runValidation, $attributeNames)) {
            if ($cascade) {
                HelperReSave::reSaveIdentifierLinks($this);
                HelperReSave::reSaveIdentifierDetails($this);
            }
            return true;
        }
        return false;
    }

    public function afterSave($insert, $changedAttributes)
    {
        if (!$insert) {
            //если изменили тип то в хранимых данных надо мигрировать
            if (isset($changedAttributes['type'])) {
                $old_type = $changedAttributes['type'];
                if ($old_type != $this->type) {


                    if ($old_type == Req3Identifiers::TYPE_OPER && (in_array($this->type, [Req3Identifiers::TYPE_ROLE, Req3Identifiers::TYPE_OPER_ROLE]))) {
                        $links = Yii::$app->authManager->getMapOperRoles();

                        $opers = Req3TasksDataItems::find()->andWhere(['type' => Req3Identifiers::TYPE_OPER, 'identifier_id' => $this->id])->groupBy(['value_id'])->all();
                        foreach ($opers as $oper) {
                            if (isset($links[$oper->value_id])) {
                                $role = reset($links[$oper->value_id]);
                                Req3TasksDataItems::updateAll(
                                    ['value_text' => $role, 'value_text_idx' => Str::compactOverflow($role, 255, "")],
                                    ['identifier_id' => $this->id, 'value_id' => $oper->value_id]
                                );
                            }
                        }
                    }

                    if ($old_type == Req3Identifiers::TYPE_ROLE && (in_array($this->type, [Req3Identifiers::TYPE_OPER, Req3Identifiers::TYPE_OPER_ROLE]))) {
                        $links = Yii::$app->authManager->getMapRoleOpers();

                        $roles = Req3TasksDataItems::find()->andWhere(['type' => Req3Identifiers::TYPE_ROLE, 'identifier_id' => $this->id])->groupBy(['value_text_idx'])->all();
                        foreach ($roles as $role) {
                            if (isset($links[$role->value_text])) {
                                $oper_id = reset($links[$role->value_text]);
                                Req3TasksDataItems::updateAll(
                                    ['value_id' => $oper_id],
                                    ['identifier_id' => $this->id, 'value_text_idx' => $role->value_text]
                                );
                            }
                        }
                    }

                    //изменили тип идентификатора
                    Req3TasksDataItems::updateAll(['type' => $this->type], [
                        'identifier_id' => $this->id,
                        'type'          => $old_type
                    ]);

                }
            }
        }
        parent::afterSave($insert, $changedAttributes);
    }

    public function beforeSave($insert)
    {
        if (in_array($this->type, self::TYPES_FORBIDDEN_MULTI) && $this->is_multi) {
            $this->is_multi = 0;
        }

        return parent::beforeSave($insert);
    }

    // ============================================================================
    // ============================== СОБЫТИЯ УДАЛЕНИЯ ============================
    // ============================================================================
    public function beforeDelete()
    {
        $items = Req3IdentifierOrders::find()->identifierId($this->id)->all();
        foreach ($items as $item) $item->delete();

        $items = $this->getDetails()->all();
        foreach ($items as $item) $item->delete();

        $items = $this->getIdentifier_links()->all();
        foreach ($items as $item) $item->delete();

        $items = $this->getParent_links()->all();
        foreach ($items as $item) $item->delete();

        return parent::beforeDelete();
    }

    // ============================================================================
    // ============================== ЧТО КАСАЕТСЯ OBJECT =========================
    // ============================================================================
    public function getTypeName()
    {
        $types = self::TYPES;
        $name = isset($types[$this->type]) ? $types[$this->type] : "Type{$this->type}";

        if ($this->type == self::TYPE_TEXT && $this->regexp) {
            $name = "Формат ввода: ";
            if ($this->regexp) $name .= $this->regexp->name;
            else $name .= "Текст";
        }

        if ($this->type == self::TYPE_GROUP) {
            $names = [];
            foreach ($this->identifier_links as $link) {
                if ($link->link_identifier) {
                    $names[] = $link->link_identifier->getTypeName();
                }
            }
            $name .= ": " . implode(", ", $names);
        }

        if ($this->type == self::TYPE_LIST && $this->list_group) {
            $name .= ": " . $this->list_group->name;
            if ($this->list_group->is_complemented) {
                $name .= " [c возможностью ввода своих значений]";
            }
        }

        if ($this->type == self::TYPE_LIST_TREE && $this->list_tree_group) {
            $name .= ": " . $this->list_tree_group->name;
        }

        return $name;
    }

    public function getAvailableTypesChange()
    {
        if ($this->isNewRecord) {
            return self::TYPES;
        }

        $types = [$this->type => isset(self::TYPES[$this->type]) ? self::TYPES[$this->type] : "type{$this->type}"];
        foreach (self::TYPES_COMPATIBLE as $group) {
            if (in_array($this->type, $group)) {
                foreach ($group as $type) {
                    if (!isset($types[$type]) && isset(self::TYPES[$type])) {
                        $types[$type] = self::TYPES[$type];
                    }
                }
            }
        }
        return $types;
    }

    public function isVisibleSimpleSettingStep(Req3Tasks $task)
    {
        return $task->step && $task->step->isVisibleSimpleSetting($this);
    }


    public function isVisibleNotSimpleSettingStep(Req3Tasks $task)
    {
        $taskService = Yii::$container->get(ProcessTaskService::class);
        $tree = null;
        $ruleData = $taskService->processRule2($task, $task->step_id, $tree, true, false);
        $taskData = $taskService->getData($task);

        $identifiers = $taskService->checkIdentifierFillOrFilter($ruleData->identifiers, $taskData);
        if (isset($identifiers[$this->id])) {
            return !$identifiers[$this->id]->isOnlySimpleStep;
        }

        return false;
    }

    public function isHideSpoiler()
    {
        return $this->getSettingByKey(self::SETTING_SPOILER, 0) == 1;
    }

    public function isCustomView()
    {
        if (in_array($this->type, self::TYPES_CUSTOM_VIEW)) {
            if ($this->type == self::TYPE_LIST) {
                return $this->getSettingByKey(self::SETTING_IS_CHECKLIST, 0) == 1;
            }
            return true;
        }
        return false;
    }

    public function isCustomEdit()
    {
        if (in_array($this->type, self::TYPES_CUSTOM_EDIT)) {
            if ($this->type == self::TYPE_LIST) {
                return $this->getSettingByKey(self::SETTING_IS_CHECKLIST, 0) == 1;
            }
            return true;
        }
        return false;
    }

    public function isDeviationBlock(Req3Tasks $task, ?RuleDataDto $ruleData = null, ?TaskDataDto $taskData = null)
    {
        if (!$ruleData) {
            $taskService = Yii::$container->get(ProcessTaskService::class);
            $tree = null;
            $ruleData = $taskService->processRule2($task, $task->step_id, $tree, true, false);
        }

        $is_required = $this->isRequired($task, $ruleData);
        $is_editable = $is_required || $this->isEditable($task, $ruleData);

        if ($is_editable) {
            if (!$taskData) {
                $taskService = Yii::$container->get(ProcessTaskService::class);
                $taskData = $taskService->getData($task);
            }
            $lastOperId = $taskData->getOperId($this->id);
            if ($lastOperId) {
                $remarks = $task->getRemarksByIdentifierId($this->id, false, $lastOperId);
                if (count($remarks) > 0) {
                    return true;
                }
            }
        }

        $setting = Req3Setting::get(Req3Setting::KEY_IDENTIFIER_FOR_REVISION);
        $identifier_for_revision_id = $setting->value_array['identifier_id'] ?? null;

        if ($identifier_for_revision_id == $this->id) {
            return true;
        }

        return false;
    }

    public function isEditable(Req3Tasks $task, ?RuleDataDto $ruleData = null)
    {
        if (in_array($this->type, self::TYPES_FORBIDDEN_EDIT)) return false;

        if (!$ruleData) {
            $taskService = Yii::$container->get(ProcessTaskService::class);
            $tree = null;
            $ruleData = $taskService->processRule2($task, $task->step_id, $tree, true, false);
        }
        return $ruleData->isEditable($this->id);
    }

    public function isRequired(Req3Tasks $task, ?RuleDataDto $ruleData = null)
    {
        if (!$ruleData) {
            $taskService = Yii::$container->get(ProcessTaskService::class);
            $tree = null;
            $ruleData = $taskService->processRule2($task, $task->step_id, $tree, true, false);
        }
        return $ruleData->isRequired($this->id);
    }

    public function isUseInDateOverdue(Req3Tasks $task)
    {
        if ($task->step && $task->step->execute_from_type == Req3TemplateSteps::EXECUTE_FROM_IDENTIFIER && $task->step->execute_from_id == $this->id) {
            return true;
        }
        return false;
    }

    public function isUseInRoles(Req3Tasks $task)
    {
        if (in_array($this->type, Req3TemplateStepRoles::ROLE_IDENTIFIER_ALLOWED)) {
            $ids = [];

            $fnct_get = function ($step) use (&$ids, &$fnct_get) {
                if ($step) {
                    /** @var $step Req3TemplateSteps */
                    if ($step->is_auto) {

                    } else {
                        $_ids = $step->getRoleIdentifierIds();
                        foreach ($_ids as $id) {
                            $ids[$id] = $id;
                        }
                    }
                }
            };
            $fnct_get($task->step);
            return in_array($this->id, $ids);
        }

        if ($this->type == self::TYPE_GROUP) {
            foreach ($this->identifiers as $identifier) {
                if ($identifier->isUseInRoles($task)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function isUseInPriority(Req3Tasks $task)
    {
        if ($task->step) {
            list($_, $data) = $task->step->getPriorityData();
            if (count($data) > 0) {
                $ids = ArrayHelper::getColumn($data, 'id');
                return in_array($this->id, $ids);
            }
        }
        return false;
    }

    public function isUseInQueueLabel(Req3Tasks $task)
    {
        if ($task->step) {
            if ($task->step->queue_label_from_type == Req3TemplateSteps::QUEUE_LABEL_FROM_IDENTIFIER) {
                return $this->id == $task->step->queue_label_from_id || $this->id == $task->step->queue_label_from_id2;
            }
        }
        return false;
    }

    public function isCompareTypes($types)
    {
        if ($types === null) return true;
        foreach ($types as $type) {
            if ($this->isCompareType($type)) return true;
        }
        return false;
    }

    public function isCompareType($type)
    {
        if ($this->type == $type) return true;
        if (is_array($type) && $this->type == self::TYPE_GROUP) {

            $sub_identifiers = $this->identifiers;

            foreach ($type as $type_sub) {
                $ok = false;
                foreach ($sub_identifiers as $key => $sub_identifier) {
                    if ($sub_identifier->isCompareType($type_sub)) {
                        $ok = true;
                        unset($sub_identifiers[$key]);
                        break;
                    }
                }
                if (!$ok) return false;
            }
            return true;
        }
        return false;
    }

    public function isMultiBlock()
    {
        return $this->is_multi && !in_array($this->type, Req3Identifiers::TYPES_MULTI_SIMPLE);
    }

    public function isMultiSimple()
    {
        return $this->is_multi && in_array($this->type, Req3Identifiers::TYPES_MULTI_SIMPLE);
    }

    public function getSettingArray()
    {
        $data = [];
        if (!empty($this->setting_json)) {
            try {
                $data = json_decode($this->setting_json, true);
            } catch (Exception $ignore) {
            }
        }
        if (!is_array($data)) $data = [];
        return $data;
    }

    public function getSettingByKey($type, $default_value = null)
    {
        $setting = $this->getSettingArray();
        return array_key_exists($type, $setting) ? $setting[$type] : $default_value;
    }

    public function getSimpleDetailsValues(Req3TasksDataItems $data_item, $ignore_details = [], $force_details = [])
    {
        $details = $this->details;

        if (in_array($data_item->type, [Req3Identifiers::TYPE_USER])) {
            if (count($details) == 0) {
                $details[] = new Req3IdentifierDetails(['type' => Req3IdentifierDetails::TYPE_USER_ACCOUNT_ID]);
                $details[] = new Req3IdentifierDetails(['type' => Req3IdentifierDetails::TYPE_USER_COUNTERPARTY_CLIENT_SERVICE_PROVIDER]);
            }
        }

        if (isset($force_details[$data_item->type])) {
            $details = $force_details[$data_item->type];
        }

        $ignore = [];
        if (isset($ignore_details[$data_item->type])) {
            $ignore = $ignore_details[$data_item->type];
        }

        $items = [];
        foreach ($details as $detail) {
            $items[] = $detail->getSimpleDetailsValue($data_item, $ignore);
        }
        return $items;
    }

    // ============================================================================
    // ============================== STATIC ======================================
    // ============================================================================
    /**
     * @param self[] $identifiers
     * @param        $types
     * @return self[]
     */
    public static function filterIdentifiers($identifiers, $types)
    {
        $result = [];
        foreach ($identifiers as $identifier) {
            if ($identifier->isCompareTypes($types)) {
                $result[] = $identifier;
            }
        }
        return $result;
    }

    public static function getForSelect2($version_id, $identifiers = null, $group = true, $types = [])
    {
        if (is_array($identifiers)) {
            $data = $identifiers;
        } else {

            $query = Req3Identifiers::find()
                ->versionId($version_id, true)
                ->indexBy('id')
                ->select(['id', 'name', 'template_id'])
                ->orderBy(['name' => SORT_ASC]);
            if (!empty($types)) {
                $query->andWhere(['type' => $types]);
            }
            $data = $query->all();
        }

        $global = [];
        $local = [];
        foreach ($data as $item) {
            if ($item['template_id'] == null) {
                $global[$item['id']] = $item['name'];
            } else {
                $local[$item['id']] = $item['name'];
            }
        }

        $result = [];
        if (!$group) {
            foreach ($local as $id => $value) $result[$id] = $value;
            foreach ($global as $id => $value) $result[$id] = $value;
        } else {
            if (count($local) == 0 || count($global) == 0) {
                $result = count($local) > 0 ? $local : $global;
            } else {
                if (count($local) > 0) $result['Локальные'] = $local;
                if (count($global) > 0) $result['Глобальные'] = $global;
            }
        }

        return $result;
    }

    public static function getMapTypes($template_id)
    {
        return Req3Identifiers::find()
            ->versionId($template_id, true)
            ->indexBy('id')
            ->select('type')
            ->column();
    }

    /**
     * @param Req3Identifiers $identifier1
     * @param Req3Identifiers $identifier2
     * @return bool
     */
    public static function isEqualsType($identifier1, $identifier2)
    {
        if ($identifier1->type != $identifier2->type) return false;

        if ($identifier1->type == Req3Identifiers::TYPE_GROUP) {
            if (count($identifier1->identifier_links) != count($identifier2->identifier_links)) return false;
            $identifier_links2 = $identifier2->identifier_links;
            foreach ($identifier1->identifier_links as $link) {
                if ($link->link_identifier) {
                    foreach ($identifier_links2 as $key2 => $link2) {
                        if ($link2->link_identifier) {
                            if (Req3Identifiers::isEqualsType($link->link_identifier, $link2->link_identifier)) {
                                unset($identifier_links2[$key2]);
                                continue 2;
                            }
                        }
                    }
                    return false;
                }
            }
            //если 0 значит всё совпало в группе
            return count($identifier_links2) == 0;
        }

        if ($identifier1->type == Req3Identifiers::TYPE_LIST) {
            return $identifier1->type_info == $identifier2->type_info;
        }

        if ($identifier1->type == Req3Identifiers::TYPE_LIST_TREE) {
            return $identifier1->type_info == $identifier2->type_info;
        }

        return true;
    }

    /**
     * @param Req3Identifiers[] $identifiers
     * @param                   $to_template_id
     * @param                   $to_version_id
     * @param                   $map_identifiers
     */
    public static function copyTo($identifiers, $to_template_id, $to_version_id, &$map_identifiers)
    {
        //сохраняем идентификаторы
        $map_identifier_links = [];
        foreach ($identifiers as $identifier) {
            $new_identifier = new Req3Identifiers();
            $new_identifier->load($identifier->attributes, '');
            $new_identifier->template_id = $to_template_id;
            $new_identifier->version_id = $to_version_id;
            if ($new_identifier->save()) {
                $map_identifiers[$identifier->id] = $new_identifier->id;

                //сохраняем отображаемые данные по идентификатору
                foreach ($identifier->details as $detail) {
                    $new_detail = new Req3IdentifierDetails();
                    $new_detail->load($detail->attributes, '');
                    $new_detail->identifier_id = $new_identifier->id;
                    $new_detail->save();
                }

                //т.к. связь может быть с идентификатором который еще не пересохранил, то записываем в карту и после сохранения ниже сохраняем это
                foreach ($identifier->identifier_links as $identifier_link) {
                    $map_identifier_links[] = ['identifier_id' => $identifier_link->identifier_id, 'link_identifier_id' => $identifier_link->link_identifier_id];
                }
            }
        }

        //сохраняем доп связи между идентификаторамии (например групповой)
        foreach ($map_identifier_links as $map_identifier_link) {
            $identifier_id = $map_identifier_link['identifier_id'];
            $link_identifier_id = $map_identifier_link['link_identifier_id'];
            if (isset($map_identifiers[$identifier_id]) && isset($map_identifiers[$link_identifier_id])) {
                $link = new Req3IdentifierLinks();
                $link->identifier_id = $map_identifiers[$identifier_id];
                $link->link_identifier_id = $map_identifiers[$link_identifier_id];
                $link->save();
            }
        }
    }

}
