<?php

namespace app\modules\process\models\identifiers;

use app\components\Date;
use app\components\Internet;
use app\modules\acs\dto\AcsDeviceDto;
use app\modules\acs\enrichers\AcsHouseEnricher;
use app\modules\acs\services\AcsService;
use app\modules\address\models\Locations;
use app\modules\communication\ModuleCommunication;
use app\modules\communication\services\CommunicationService;
use app\modules\counterparties\dto\CounterpartyDto;
use app\modules\counterparties\models\Counterparties;
use app\modules\document\models\Documents;
use app\modules\edm\models\EdmDoc;
use app\modules\ktv\dto\KtvDeviceDto;
use app\modules\ktv\services\KtvService;
use app\modules\payments\models\Bonuses;
use app\modules\process\models\_query\Req3IdentifierDetailsQuery;
use app\modules\process\models\task_data\Req3TasksDataItems;
use app\modules\process\services\ProcessDowntimeService;
use app\modules\user\dto\UserCounterpartiesLinkDto;
use app\modules\user\services\UserCounterpartyService;
use app\modules\utm\models\BlocksInfo;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Url;

/**
 * This is the model class for table "req3_identifier_details".
 *
 * @property integer         $id
 * @property integer         $identifier_id
 * @property integer         $type
 *
 * @property Req3Identifiers $identifier
 */
class Req3IdentifierDetails extends ActiveRecord
{
    // ============================================================================
    // ============================== КОНСТАНТЫ ===================================
    // ============================================================================
    const TYPE_USER_LOGIN                                = 1;
    const TYPE_USER_SERVICES                             = 5;
    const TYPE_USER_TARIFFS                              = 6;
    const TYPE_USER_BONUS                                = 7;
    const TYPE_USER_ADDRESS                              = 8;
    const TYPE_USER_WH_BALANCE                           = 9;
    const TYPE_USER_BLOCK                                = 10;
    const TYPE_USER_BALANCE                              = 11;
    const TYPE_USER_ACCOUNT_ID                           = 12;
    const TYPE_USER_FM                                   = 14;
    const TYPE_USER_NEXT_TARIFF                          = 17;
    const TYPE_USER_DATE_CREATE                          = 18;
    const TYPE_USER_MAC                                  = 19;
    const TYPE_USER_DOC_STATUS                           = 20;
    const TYPE_USER_TYPE                                 = 21;
    const TYPE_USER_ONU                                  = 22;
    const TYPE_USER_DP                                   = 23;
    const TYPE_USER_COUNTERPARTY_CLIENT_SERVICE_PROVIDER = 2;//заказчик_услуги
    const TYPE_USER_COUNTERPARTY_SERVICE_RECIPIENT       = 25;//получатели_услуги
    const TYPE_USER_COUNTERPARTY_SERVICE_PROVIDER        = 26;//поставщик_услуги
    const TYPE_USER_COUNTERPARTY_PROPERTY_OWNER          = 27;//владелец_помещения
    const TYPE_USER_CAP                                  = 28; // ТКД
    const TYPE_USER_CONNECTION_TYPE                      = 29; // Тип подключения
    const TYPE_USER_DOWNTIME = 30; // время простоя абонента
    const TYPE_USER_ACS      = 40;
    const TYPE_USER_KTV      = 41;

    const TYPE_USER_NAMES = [
        self::TYPE_USER_ACCOUNT_ID                           => 'ЛС',
        self::TYPE_USER_LOGIN                                => 'Логин',
        self::TYPE_USER_DATE_CREATE                          => 'Дата создания',
        self::TYPE_USER_BALANCE                              => 'Баланс',
        self::TYPE_USER_BONUS                                => 'Бонус',
        self::TYPE_USER_ADDRESS                              => 'Адрес',
        self::TYPE_USER_FM                                   => 'Фин Менеджер',
        self::TYPE_USER_BLOCK                                => 'Блокировка',
        self::TYPE_USER_SERVICES                             => 'Услуги',
        self::TYPE_USER_TARIFFS                              => 'Тарифы',
        self::TYPE_USER_NEXT_TARIFF                          => 'Следующий тариф',
        self::TYPE_USER_DP                                   => 'РП',
        self::TYPE_USER_WH_BALANCE                           => 'Баланс склада',
        self::TYPE_USER_MAC                                  => 'Подтвержденный MAC',
        self::TYPE_USER_DOC_STATUS                           => 'Статус договора',
        self::TYPE_USER_TYPE                                 => 'Тип абонента',
        self::TYPE_USER_ONU                                  => 'ОНУ',
        self::TYPE_USER_COUNTERPARTY_CLIENT_SERVICE_PROVIDER => 'Заказчик услуги',
        self::TYPE_USER_COUNTERPARTY_SERVICE_RECIPIENT       => 'Получатели услуги',
        self::TYPE_USER_COUNTERPARTY_SERVICE_PROVIDER        => 'Поставщик услуги',
        self::TYPE_USER_COUNTERPARTY_PROPERTY_OWNER          => 'Владелец помещения',
        self::TYPE_USER_CAP                                  => 'ТКД',
        self::TYPE_USER_CONNECTION_TYPE => 'Тип подключения',
        self::TYPE_USER_DOWNTIME        => 'Время просто абонента (30 дней)',
        self::TYPE_USER_ACS             => 'СКУД',
        self::TYPE_USER_KTV             => 'КТВ',
    ];

    const TYPE_COUNTERPARTY_NAME                    = 1;
    const TYPE_COUNTERPARTY_USER_ACCOUNTS           = 2;
    const TYPE_COUNTERPARTY_INN                     = 3;
    const TYPE_COUNTERPARTY_JUR_ADDRESS             = 4;
    const TYPE_COUNTERPARTY_MAIL_ADDRESS            = 5;
    const TYPE_COUNTERPARTY_COMMUNICATIONS          = 6;
    const TYPE_COUNTERPARTY_REPRESENTATIVE_NAME     = 8;
    const TYPE_COUNTERPARTY_REPRESENTATIVE_POSITION = 9;
    const TYPE_COUNTERPARTY_DOC_TYPE                = 10;

    const TYPE_COUNTERPARTY_NAMES = [
        self::TYPE_COUNTERPARTY_NAME                    => 'Наименование',
        self::TYPE_COUNTERPARTY_USER_ACCOUNTS           => 'Привязанные лицевые счета',
        self::TYPE_COUNTERPARTY_INN                     => 'ИНН',
        self::TYPE_COUNTERPARTY_JUR_ADDRESS             => 'Юридический адрес',
        self::TYPE_COUNTERPARTY_MAIL_ADDRESS            => 'Почтовый адрес',
        self::TYPE_COUNTERPARTY_COMMUNICATIONS          => 'Каналы связи',
        self::TYPE_COUNTERPARTY_REPRESENTATIVE_NAME     => 'ФИО представителя',
        self::TYPE_COUNTERPARTY_REPRESENTATIVE_POSITION => 'Должность представителя',
        self::TYPE_COUNTERPARTY_DOC_TYPE                => 'Тип документооборота'
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
        return 'req3_identifier_details';
    }

    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'identifier_id' => 'Identifier ID',
            'type'          => 'Type',
        ];
    }

    public static function find()
    {
        return new Req3IdentifierDetailsQuery(get_called_class());
    }

    // ============================================================================
    // ============================== ПРАВИЛА =====================================
    // ============================================================================

    public function rules()
    {
        return [
            [['identifier_id', 'type'], 'required'],
            [['identifier_id', 'type'], 'integer'],
        ];
    }

    // ============================================================================
    // ============================== СЦЕНАРИИ ====================================
    // ============================================================================

    // ============================================================================
    // ============================== ГЕТТЕРЫ =====================================
    // ============================================================================
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
    public function getUserTypeName()
    {
        $types = self::TYPE_USER_NAMES;
        if (isset($types[$this->type])) return $types[$this->type];
        return "Type{$this->type}";
    }


    public function getCounterpartyTypeName()
    {
        $types = self::TYPE_COUNTERPARTY_NAMES;
        if (isset($types[$this->type])) return $types[$this->type];
        return "Type{$this->type}";
    }

    public function getSimpleDetailsValue(Req3TasksDataItems $data_item, $ignore_details = [])
    {
        if ($data_item->type == Req3Identifiers::TYPE_USER) {
            return $this->getSimpleDetailsValueUser($data_item, $ignore_details);
        }
        if ($data_item->type == Req3Identifiers::TYPE_COUNTERPARTIES) {
            return $this->getSimpleDetailsValueCounterparty($data_item, $ignore_details);
        }

        return [
            'type'  => $this->type,
            'name'  => "type_{$this->type}",
            'value' => "",//заглушка, изменится на нормализованное значение
            'link'  => null,//заглушка, тут будет ссылка если имеется
        ];

    }

    public function getSimpleDetailsValueUser(Req3TasksDataItems $data_item, $ignore_details = [])
    {
        $data = [
            'type'  => $this->type,
            'name'  => $this->getUserTypeName(),
            'value' => "",//заглушка, изменится на нормализованное значение
            'link'  => null,//заглушка, тут будет ссылка если имеется
        ];

        if ($data_item->user) {

            if ($this->type == self::TYPE_USER_LOGIN && !in_array($this->type, $ignore_details)) {
                $data['link'] = Url::toRoute(["/user1/profile/view", 'user_id' => $data_item->value_id], true);
                $data['value'] = $data_item->user->login;
            }

            if ($this->type == self::TYPE_USER_ADDRESS && !in_array($this->type, $ignore_details)) {
                $data['value'] = $data_item->user->getAddressFullName(Locations::TYPE_COUNTRY, true, true);
            }

            if ($this->type == self::TYPE_USER_BONUS && !in_array($this->type, $ignore_details)) {
                if ($data_item->user->bonus) {
                    $data['value'] = $data_item->user->bonus->getName() . " " . $data_item->user->bonus->amount . ($data_item->user->bonus->amount_type == Bonuses::AMOUNT_TYPE_PERCENT ? "%" : "");
                    $data['value'] .= " (" . ($data_item->user->bonus->oper->login ?? "Del{$data_item->user->bonus->creator_id}") . ": " . $data_item->user->bonus->comment . ")";
                }
            }

            if ($this->type == self::TYPE_USER_SERVICES && !in_array($this->type, $ignore_details)) {
                $texts = [];
                $services = $data_item->user->getUTMServices();
                foreach ($services as $service) {
                    $texts[] = $service['name'];
                }
                $data['value'] = implode(", ", $texts);
            }

            if ($this->type == self::TYPE_USER_TARIFFS && !in_array($this->type, $ignore_details)) {
                $texts = [];
                $tariffs = $data_item->user->getUTMTariffs();
                foreach ($tariffs as $tariff) {
                    $texts[] = $tariff['name'];
                }
                $data['value'] = implode(", ", $texts);
            }

            if ($this->type == self::TYPE_USER_WH_BALANCE && !in_array($this->type, $ignore_details)) {
                if ($data_item->user->balance) {
                    $texts = [];
                    foreach ($data_item->user->balance->balance_item as $balance_item) {
                        $texts[] = ($balance_item->item->name ?? "item_{$balance_item->item_id}") . " (" . $balance_item->amount . ")";
                    }
                    $data['value'] = implode(", ", $texts);
                }
            }

            if ($this->type == self::TYPE_USER_BLOCK && !in_array($this->type, $ignore_details)) {
                if ($data_item->user->utm_account) {
                    if ($data_item->user->utm_account->block_id == 0) {
                        $data['value'] = "Разблочен";
                    } elseif ($data_item->user->utm_account->block_info) {
                        if ($data_item->user->utm_account->block_info->block_type == BlocksInfo::BLOCK_TYPE_SYSTEM) {
                            $data['value'] = "Системная";
                        } else {
                            $data['value'] = "Админская";
                        }
                        $data['value'] .= " (" . date("d.m.Y H:i", $data_item->user->utm_account->block_info->start_date) . ")";
                    } else {
                        $data['value'] = "⚠️ заблочен но нет инфы о блокировке";
                    }
                } else {
                    $data['value'] = "⚠️ Странно, в UTM нет аккаунта";
                }
            }

            if ($this->type == self::TYPE_USER_BALANCE && !in_array($this->type, $ignore_details)) {
                if ($data_item->user->utm_account) {
                    $data['value'] = number_format($data_item->user->utm_account->balance, 2, ',', ' ') . "руб.";
                    if ($data_item->user->utm_account->block_info && $data_item->user->utm_account->block_info->block_type == BlocksInfo::BLOCK_TYPE_ADMIN) {
                        if ($data_item->user->adm_block_current) {
                            $data['value'] .= " (До блокировки:" . number_format($data_item->user->adm_block_current->prev_balance, 2, ',', ' ') . "руб.)";
                        }
                    }
                }
            }

            if ($this->type == self::TYPE_USER_ACCOUNT_ID && !in_array($this->type, $ignore_details)) {
                $data['link'] = Url::toRoute(["/user1/profile/view", 'user_id' => $data_item->value_id], true);
                $data['value'] = $data_item->user->utm_acc_id;
            }

            if ($this->type == self::TYPE_USER_FM) {
                $data['value'] = $data_item->user->fm->fio ?? "[fm not defined]";
            }

            if ($this->type == self::TYPE_USER_NEXT_TARIFF && !in_array($this->type, $ignore_details)) {
                $texts = [];
                foreach ($data_item->user->account_tariff_links as $account_tariff_link) {
                    $texts[] = $account_tariff_link->next_tariff->name ?? "tariff_{$account_tariff_link->next_tariff_id}";
                }
                $data['value'] = implode(", ", $texts);
            }

            if ($this->type == self::TYPE_USER_DATE_CREATE && !in_array($this->type, $ignore_details)) {
                $date = new Date($data_item->user->create_time);
                $data['value'] = $date->format(Date::FORMAT_DATE_TIME) . " (" . $date->toRemainingText(2) . ")";
            }

            if ($this->type == self::TYPE_USER_MAC && !in_array($this->type, $ignore_details)) {
                $data['value'] = $data_item->user->comp ? Internet::toMacDelimiter($data_item->user->comp->mac) : "[comp not found]";
            }

            if ($this->type == self::TYPE_USER_TYPE) {
                $data['value'] = $data_item->user->getUserTypeName();
            }

            if ($this->type == self::TYPE_USER_DOC_STATUS && !in_array($this->type, $ignore_details)) {
                $edm = [];
                $docs = [];
                foreach ($data_item->user->edm_docs as $doc) {
                    if ($doc->type == EdmDoc::DOC_TYPE_CONTRACT && $doc->status != EdmDoc::DOC_STATUS_ARCHIVED) {
                        $edm[] = $doc;
                    }
                }
                if (count($edm) == 0) {
                    foreach ($data_item->user->documents as $document) {
                        if ($document->type == Documents::TYPE_CONTRACT && in_array($document->trigger_type, [Documents::TRIGGER_TYPE_CONNECTION_ACTIVATION, Documents::TRIGGER_TYPE_MANUAL])) {
                            if ($document->status == Documents::STATUS_DONE) {
                                $docs[] = $document;
                            }
                        }
                    }
                }

                if (count($edm) > 0) {
                    $data['link'] = Url::toRoute(['/edm/data/index', 'user_ids' => [$data_item->user->user_id]], true);
                    $data['value'] = "ЭДО";
                    $texts = [];
                    foreach ($edm as $doc) {
                        $texts [] = $doc->getStatusName();
                    }
                    $data['value'] .= " (" . implode(", ", $texts) . ")";
                }

                if (count($docs) > 0) {
                    $data['link'] = Url::toRoute(['/document/documents/index', 'user_ids' => [$data_item->user->user_id]], true);
                    $data['value'] = "Бумажный";
                }

                if (count($edm) == 0 && count($docs) == 0) {
                    $data['value'] = "Договора нет";
                }
            }

            if ($this->type == self::TYPE_USER_DP) {
                $dp = $data_item->user->getNextDP();
                $data['value'] = date("Y-m-d H:i:s", $dp);
            }

            if (in_array($this->type, [
                self::TYPE_USER_COUNTERPARTY_CLIENT_SERVICE_PROVIDER,
                self::TYPE_USER_COUNTERPARTY_SERVICE_RECIPIENT,
                self::TYPE_USER_COUNTERPARTY_SERVICE_PROVIDER,
                self::TYPE_USER_COUNTERPARTY_PROPERTY_OWNER,])) {
                $userCounterpartyService = Yii::$container->get(UserCounterpartyService::class);
                $counterpartyGroup = $userCounterpartyService->getGroupCounterpartiesByUserId($data_item->value_id);

                if ($this->type == self::TYPE_USER_COUNTERPARTY_CLIENT_SERVICE_PROVIDER) {
                    if ($counterpartyGroup->counterpartyClientServiceProvider && $counterpartyGroup->counterpartyClientServiceProvider->hasCounterpartyInfo()) {
                        $data['link'] = Url::toRoute(['/counterparties/counterparties/view', 'id' => $counterpartyGroup->counterpartyClientServiceProvider->counterpartyId], true);
                        $data['value'] = $counterpartyGroup->counterpartyClientServiceProvider->getCounterpartyInfo()->getName();
                    } else {
                        $data['value'] = "Заказчик услуги не указан";
                    }
                }

                if ($this->type == self::TYPE_USER_COUNTERPARTY_SERVICE_RECIPIENT) {
                    $fioCounterpartyServiceRecipients = array_map(
                        fn(CounterpartyDto $c) => $c->getName(),
                        array_filter(
                            array_map(
                                fn(UserCounterpartiesLinkDto $link) => $link->getCounterpartyInfo(),
                                $counterpartyGroup->counterpartyServiceRecipients
                            )
                        )
                    );
                    if (count($fioCounterpartyServiceRecipients) > 0) {
                        $data['value'] = implode(", ", $fioCounterpartyServiceRecipients);
                    } else {
                        $data['value'] = "Получатели услуги не указаны";
                    }
                }

                if ($this->type == self::TYPE_USER_COUNTERPARTY_SERVICE_PROVIDER) {
                    if ($counterpartyGroup->counterpartyServiceProvider && $counterpartyGroup->counterpartyServiceProvider->hasCounterpartyInfo()) {
                        $data['link'] = Url::toRoute(['/counterparties/counterparties/view', 'id' => $counterpartyGroup->counterpartyServiceProvider->counterpartyId], true);
                        $data['value'] = $counterpartyGroup->counterpartyServiceProvider->getCounterpartyInfo()->getName();
                    } else {
                        $data['value'] = "Поставщик услуги не указан";
                    }
                }

                if ($this->type == self::TYPE_USER_COUNTERPARTY_PROPERTY_OWNER) {
                    if ($counterpartyGroup->counterpartyPropertyOwner && $counterpartyGroup->counterpartyPropertyOwner->hasCounterpartyInfo()) {
                        $data['link'] = Url::toRoute(['/counterparties/counterparties/view', 'id' => $counterpartyGroup->counterpartyPropertyOwner->counterpartyId], true);
                        $data['value'] = $counterpartyGroup->counterpartyPropertyOwner->getCounterpartyInfo()->getName();
                    } else {
                        $data['value'] = "Владелец помещения не указан";
                    }
                }
            }
            if ($this->type == self::TYPE_USER_CAP) {
                if ($data_item->user->cap->address ?? false) {
                    $data['value'] = $data_item->user->cap->address->getFullName(Locations::TYPE_COUNTRY, true, true);
                } else {
                    $data['value'] = "Не найдено информации";
                }
            }
            if ($this->type == self::TYPE_USER_CONNECTION_TYPE) {
                if ($data_item->user->connection_type ?? false) {
                    $data['value'] = $data_item->user->generateConnectionTypeText();
                } else {
                    $data['value'] = "Не найдено информации";
                }
            }
            if ($this->type == self::TYPE_USER_DOWNTIME) {
                $downtimeService = Yii::$container->get(ProcessDowntimeService::class);
                $sec = $downtimeService->getByUserId($data_item->value_id, 30);
                if ($sec > 0) {
                    $data['value'] = Date::secondsToText($sec, 2);
                } else {
                    $data['value'] = "Простоев не было";
                }
            }
            if ($this->type == self::TYPE_USER_ACS) {
                $acsService = Yii::$container->get(AcsService::class);
                $acsEnricher = Yii::$container->get(AcsHouseEnricher::class);

                $acsIds = $acsService->getDeviceIdsByUserIds([$data_item->value_id]);
                $acs = $acsService->getDeviceByIds($acsIds);
                $acsEnricher->enrichMany($acs);

                if (empty($acs)) {
                    $data['value'] = implode(", ", array_map(fn(AcsDeviceDto $it) => $it->getName(), $acs));
                } else {
                    $data['value'] = "Устройств СКУД не найдено";
                }
            }
            if ($this->type == self::TYPE_USER_KTV) {
                $ktvService = Yii::$container->get(KtvService::class);
                $ktvDevices = $ktvService->getDevicesByUserId($data_item->value_id);
                if (empty($ktvDevices)) {
                    $data['value'] = implode(", ", array_map(fn(KtvDeviceDto $it) => $it->name, $ktvDevices));
                } else {
                    $data['value'] = "Устройств КТВ не найдено";
                }
            }
        }

        return $data;
    }

    public function getSimpleDetailsValueCounterparty(Req3TasksDataItems $data_item, $ignore_details = [])
    {
        $data = [
            'type'  => $this->type,
            'name'  => $this->getCounterpartyTypeName(),
            'value' => "",//заглушка, изменится на нормализованное значение
            'link'  => null,//заглушка, тут будет ссылка если имеется
        ];

        if ($data_item->counterparty) {

            if ($this->type == self::TYPE_COUNTERPARTY_NAME && !in_array($this->type, $ignore_details)) {
                $data['link'] = Url::toRoute(['/counterparties/counterparties/view', 'id' => $data_item->counterparty->id], true);
                $data['value'] = $data_item->counterparty->getTitle();
            }

            if ($this->type == self::TYPE_COUNTERPARTY_USER_ACCOUNTS && !in_array($this->type, $ignore_details)) {
                $users = [];
                foreach ($data_item->counterparty->link_users as $link_user) {
                    if ($link_user->user) $users[] = $link_user->user->utm_acc_id;
                }
                $data['value'] = implode(", ", $users);
            }

            if ($this->type == self::TYPE_COUNTERPARTY_INN && !in_array($this->type, $ignore_details)) {
                if ($data_item->counterparty->link_type == Counterparties::LINK_TYPE_IP && $data_item->counterparty->ip) {
                    $data['value'] = $data_item->counterparty->ip->inn;
                }
                if ($data_item->counterparty->link_type == Counterparties::LINK_TYPE_ORGANIZATION && $data_item->counterparty->organization) {
                    $data['value'] = $data_item->counterparty->organization->inn;
                }
                if ($data_item->counterparty->link_type == Counterparties::LINK_TYPE_PHYS_FACE) {
                    $data['value'] = "---";
                }
            }

            if ($this->type == self::TYPE_COUNTERPARTY_JUR_ADDRESS && !in_array($this->type, $ignore_details)) {
                if ($data_item->counterparty->link_type == Counterparties::LINK_TYPE_IP) {
                    $data['value'] = $data_item->counterparty->ip->address ?? "---";
                }
                if ($data_item->counterparty->link_type == Counterparties::LINK_TYPE_ORGANIZATION) {
                    $data['value'] = $data_item->counterparty->organization->address ?? "---";
                }
                if ($data_item->counterparty->link_type == Counterparties::LINK_TYPE_PHYS_FACE) {
                    $data['value'] = "---";
                }
            }

            if ($this->type == self::TYPE_COUNTERPARTY_MAIL_ADDRESS && !in_array($this->type, $ignore_details)) {
                if ($data_item->counterparty->link_type == Counterparties::LINK_TYPE_IP) {
                    $data['value'] = $data_item->counterparty->ip->address_mailing ?? "---";
                }
                if ($data_item->counterparty->link_type == Counterparties::LINK_TYPE_ORGANIZATION) {
                    $data['value'] = $data_item->counterparty->organization->address_mailing ?? "---";
                }
                if ($data_item->counterparty->link_type == Counterparties::LINK_TYPE_PHYS_FACE) {
                    $data['value'] = "---";
                }
            }

            if ($this->type == self::TYPE_COUNTERPARTY_COMMUNICATIONS && !in_array($this->type, $ignore_details)) {
                $communicationService = Yii::$container->get(CommunicationService::class);
                $channels = $communicationService->getChannelLinksByLinkId($data_item->value_id);
                /** @var ModuleCommunication $communicationModule */
                $communicationModule = Yii::$app->getModule('communication');

                $values = [];
                foreach ($communicationModule->types as $type) {
                    if ($channels->hasChannels($type['type'])) {
                        $subValue = $type['name'] . ": ";
                        $subValues = [];
                        foreach ($channels->filterByType($type['type'])->getAll() as $channel) {
                            if ($channel->hasChannelInfo()) {
                                $subValues[] = $channel->getValue() . " (" . $channel->getChannelInfo()->getName() . ")";
                            }
                        }
                        $values[] = $subValue . implode(", ", $subValues) . ". ";
                    }
                }

                if (count($values) == 0) {
                    $data['value'] = "Нет каналов связи.";
                } else {
                    $data['value'] = implode(" / ", $values);
                }
            }

            if ($this->type == self::TYPE_COUNTERPARTY_REPRESENTATIVE_NAME && !in_array($this->type, $ignore_details)) {
                if ($data_item->counterparty->link_type == Counterparties::LINK_TYPE_IP) {
                    $data['value'] = $data_item->counterparty->ip->representative_name ?? "---";
                }
                if ($data_item->counterparty->link_type == Counterparties::LINK_TYPE_ORGANIZATION) {
                    $data['value'] = $data_item->counterparty->organization->representative_name ?? "---";
                }
                if ($data_item->counterparty->link_type == Counterparties::LINK_TYPE_PHYS_FACE) {
                    $data['value'] = "---";
                }
            }

            if ($this->type == self::TYPE_COUNTERPARTY_REPRESENTATIVE_POSITION && !in_array($this->type, $ignore_details)) {
                if ($data_item->counterparty->link_type == Counterparties::LINK_TYPE_IP) {
                    $data['value'] = $data_item->counterparty->ip->representative_position ?? "---";
                }
                if ($data_item->counterparty->link_type == Counterparties::LINK_TYPE_ORGANIZATION) {
                    $data['value'] = $data_item->counterparty->organization->representative_position ?? "---";
                }
                if ($data_item->counterparty->link_type == Counterparties::LINK_TYPE_PHYS_FACE) {
                    $data['value'] = "---";
                }
            }

            if ($this->type == self::TYPE_COUNTERPARTY_DOC_TYPE && !in_array($this->type, $ignore_details)) {
                $data['value'] = $data_item->counterparty->getDocTypeName();
            }

        }

        return $data;
    }

    // ============================================================================
    // ============================== STATIC ======================================
    // ============================================================================

}