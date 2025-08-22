<?php

use app\components\Date;
use app\components\Internet;
use app\modules\acs\dto\AcsDeviceDto;
use app\modules\acs\enrichers\AcsHouseEnricher;
use app\modules\acs\services\AcsService;
use app\modules\address\models\Locations;
use app\modules\crash\models\Request;
use app\modules\crash\models\RequestUsers;
use app\modules\document\models\Documents;
use app\modules\edm\models\EdmDoc;
use app\modules\ktv\dto\KtvDeviceDto;
use app\modules\ktv\services\KtvService;
use app\modules\payments\models\Bonuses;
use app\modules\process\models\identifiers\Req3IdentifierDetails;
use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItems;
use app\modules\process\services\ProcessDowntimeService;
use app\modules\user\services\UserCommunicationService;
use app\modules\user\services\UserCounterpartyService;
use app\modules\userside\components\UserSideHelper;
use app\modules\utm\models\BlocksInfo;
use yii\bootstrap4\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $identifier Req3Identifiers */
/* @var $value Req3TasksDataItems */

/* @var $is_editable boolean */
/* @var $is_required boolean */
/* @var $is_only_view boolean */

/* @var $can_edit boolean */

if (count($identifier->details) == 0) {
    $identifier->populateRelation('details', [
        new Req3IdentifierDetails(['type' => Req3IdentifierDetails::TYPE_USER_ACCOUNT_ID]),
        new Req3IdentifierDetails(['type' => Req3IdentifierDetails::TYPE_USER_COUNTERPARTY_CLIENT_SERVICE_PROVIDER])
    ]);
}

$userCommunicationService = Yii::$container->get(UserCommunicationService::class);
?>

<?php if ($value->user): ?>

    <?= $this->render('state_zone', ['value' => $value]) ?>

    <?php $counterpartyGroup = null;// пригодится ниже что бы не делать несколько запросов ?>
    <?php foreach ($identifier->details as $detail): ?>
        <div>
            <span style="font-weight: bolder"><?= $detail->getUserTypeName() ?>:</span>

            <?php //==============================================================?>
            <?php if ($detail->type == Req3IdentifierDetails::TYPE_USER_LOGIN): ?>
                <a href="<?= Url::toRoute(["/user1/profile/view", 'user_id' => $value->user->user_id]) ?>"
                   target="_blank"><?= $value->user->login ?></a>
            <?php endif; ?>
            <?php //==============================================================?>
            <?php if ($detail->type == Req3IdentifierDetails::TYPE_USER_ADDRESS): ?>
                <?= $value->user->getAddressFullName(Locations::TYPE_COUNTRY, true, true) ?>
            <?php endif; ?>
            <?php //==============================================================?>
            <?php if ($detail->type == Req3IdentifierDetails::TYPE_USER_BONUS): ?>
                <?php if ($value->user->bonus): ?>
                    <?= $value->user->bonus->getName() ?>
                    <span style="color: #007b1e">
                        <?= $value->user->bonus->amount ?><?= $value->user->bonus->amount_type == Bonuses::AMOUNT_TYPE_PERCENT ? "%" : "" ?>
                    </span>
                    <span style="font-size: small; color: #868686">
                        (<?= ($value->user->bonus->oper->login ?? "Del{$value->user->bonus->creator_id}") . ": " . $value->user->bonus->comment ?>)
                    </span>
                <?php endif; ?>
            <?php endif; ?>
            <?php //==============================================================?>
            <?php if ($detail->type == Req3IdentifierDetails::TYPE_USER_SERVICES): ?>
                <?php $services = $value->user->getUTMServices(); ?>
                <?php foreach ($services as $service): ?>
                    <?php if (count($services) > 1) echo '<div style="text-indent :1em;">'; ?>
                    <?= $service['name']; ?>
                    <?php if (count($services) > 1) echo '</div>'; ?>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php //==============================================================?>
            <?php if ($detail->type == Req3IdentifierDetails::TYPE_USER_TARIFFS): ?>
                <?php $tariffs = $value->user->getUTMTariffs(); ?>
                <?php foreach ($tariffs as $tariff): ?>
                    <?php if (count($tariffs) > 1) echo '<div style="text-indent :1em;">'; ?>
                    <?= $tariff['name'] ?>
                    <?php if (count($tariffs) > 1) echo '</div>'; ?>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php //==============================================================?>
            <?php if ($detail->type == Req3IdentifierDetails::TYPE_USER_WH_BALANCE): ?>
                <?php if ($value->user->balance): ?>
                    <?php foreach ($value->user->balance->balance_item as $balance_item): ?>
                        <div style="text-indent :1em;">
                            <?= $balance_item->item ? $balance_item->item->name : "Deleted" ?>
                            <span style="color: #0a99b5">
                                (<?= $balance_item->amount ?>)
                            </span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endif; ?>
            <?php //==============================================================?>
            <?php if ($detail->type == Req3IdentifierDetails::TYPE_USER_BLOCK): ?>
                <?php if ($value->user->utm_account): ?>
                    <?php if ($value->user->utm_account->block_id == 0): ?>
                        <span style="color: #00c006;">Разблочен</span>
                    <?php elseif ($value->user->utm_account->block_info): ?>
                        <?php if ($value->user->utm_account->block_info->block_type == BlocksInfo::BLOCK_TYPE_SYSTEM): ?>
                            <span style="color: #b46e00;">Системная</span>
                        <?php else: ?>
                            <span style="color: #b42d00;">Админская</span>
                        <?php endif; ?>
                        <span style="color: #9a9a9a;">(<?= date("d.m.Y H:i", $value->user->utm_account->block_info->start_date) ?>)</span>
                    <?php else: ?>
                        <span style="color: #e22500; font-weight: bold;"><?= "⚠️" ?> заблочен но нет инфы о блокировке</span>
                    <?php endif; ?>
                <?php else: ?>
                    <span style="color: #e22500; font-weight: bold;"><?= "⚠️" ?> Забавно в UTM не нашел аккаунт</span>
                <?php endif; ?>
            <?php endif; ?>
            <?php //==============================================================?>
            <?php if ($detail->type == Req3IdentifierDetails::TYPE_USER_BALANCE): ?>
                <?php if ($value->user->utm_account): ?>
                    <span style="color: #012fb4;"><?= number_format($value->user->utm_account->balance, 2, ',', ' ') . "руб." ?></span>
                    <?php if ($value->user->utm_account->block_info && $value->user->utm_account->block_info->block_type == BlocksInfo::BLOCK_TYPE_ADMIN): ?>
                        <?php if ($value->user->adm_block_current): ?>
                            <span style="color: #b41200;">
                                (<span style="color: #ab4126;">До блокировки:</span>
                                <?= number_format($value->user->adm_block_current->prev_balance, 2, ',', ' ') . "руб." ?>)
                            </span>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
            <?php //==============================================================?>
            <?php if ($detail->type == Req3IdentifierDetails::TYPE_USER_ACCOUNT_ID): ?>
                <a href="<?= Url::toRoute(["/user1/profile/view", 'user_id' => $value->user->user_id]) ?>"
                   target="_blank"><?= $value->user->utm_acc_id ?></a>
            <?php endif; ?>
            <?php //==============================================================?>
            <?php if ($detail->type == Req3IdentifierDetails::TYPE_USER_FM): ?>
                <?php $fm = $value->user->fm; ?>
                <?php if ($fm): ?>
                    <?= $fm->fio ?>
                <?php endif; ?>
            <?php endif; ?>
            <?php //==============================================================?>
            <?php if ($detail->type == Req3IdentifierDetails::TYPE_USER_NEXT_TARIFF): ?>
                <?php $tariff_i = 0; ?>
                <?php foreach ($value->user->account_tariff_links as $i => $account_tariff_link): ?>
                    <?php if ($account_tariff_link->next_tariff): ?>
                        <?php if ($tariff_i != 0) echo '<div style="text-indent :1em;">'; ?>
                        <?= $account_tariff_link->next_tariff->name ?>
                        <?php if ($tariff_i++ != 0) echo '</div>'; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php //==============================================================?>
            <?php if ($detail->type == Req3IdentifierDetails::TYPE_USER_DP): ?>
                <?php $dp = $value->user->getNextDP(); ?>
                <?php if ($dp !== null): ?>
                    <?php $dp = new Date($dp); ?>
                    <?= $dp->format(Date::FORMAT_DATE_TIME) ?> <span
                            style="color: #727272; font-size: small">(<?= $dp->toRemainingText(1) ?>)</span>
                <?php endif; ?>
            <?php endif; ?>
            <?php //==============================================================?>
            <?php if ($detail->type == Req3IdentifierDetails::TYPE_USER_DATE_CREATE): ?>
                <?php $date = new Date($value->user->create_time) ?>
                <?= $date->format(Date::FORMAT_DATE_TIME) ?>
                <span style="color: #858585; font-size: small">(<?= $date->toRemainingText(2) ?>)</span>
            <?php endif; ?>
            <?php //==============================================================?>
            <?php if ($detail->type == Req3IdentifierDetails::TYPE_USER_MAC): ?>
                <?= $value->user->comp ? Internet::toMacDelimiter($value->user->comp->mac) : "" ?>
            <?php endif; ?>
            <?php //==============================================================?>
            <?php if ($detail->type == Req3IdentifierDetails::TYPE_USER_TYPE): ?>
                <?= $value->user->getUserTypeName() ?>
            <?php endif; ?>
            <?php //==============================================================?>
            <?php if ($detail->type == Req3IdentifierDetails::TYPE_USER_DOC_STATUS): ?>

                <?php
                $edm = [];
                $docs = [];
                foreach ($value->user->edm_docs as $doc) {
                    if ($doc->type == EdmDoc::DOC_TYPE_CONTRACT && $doc->status != EdmDoc::DOC_STATUS_ARCHIVED) {
                        $edm[] = $doc;
                    }
                }
                if (count($edm) == 0) {
                    foreach ($value->user->documents as $document) {
                        if ($document->type == Documents::TYPE_CONTRACT && in_array($document->trigger_type, [Documents::TRIGGER_TYPE_CONNECTION_ACTIVATION, Documents::TRIGGER_TYPE_MANUAL])) {
                            if ($document->status == Documents::STATUS_DONE) {
                                $docs[] = $document;
                            }
                        }
                    }
                }
                ?>

                <?php if (count($edm) > 0): ?>
                    <?= Html::a("ЭДО", ['/edm/data/index', 'user_ids' => [$value->user->user_id]], ['target' => "_blank"]) ?>
                    <?php foreach ($edm as $doc): ?>
                        (<?= $doc->getStatusName() ?>)
                    <?php endforeach; ?>
                <?php endif; ?>


                <?php if (count($docs) > 0): ?>
                    <?= Html::a("Бумажный", ['/document/documents/index', 'user_ids' => [$value->user->user_id]], ['target' => "_blank"]) ?>
                <?php endif; ?>

                <?php if (count($edm) == 0 && count($docs) == 0): ?>
                    <span style="color: #e43e34">Договора нет</span>
                <?php endif; ?>
            <?php endif; ?>
            <?php //==============================================================?>
            <?php if ($detail->type == Req3IdentifierDetails::TYPE_USER_ONU): ?>
                <?php $items = $value->user->getCompUsersideOnuData() ?>


                <?php foreach ($items as $item): ?>
                    <div><span style="font-weight: bold; color: #2c5ba7;">Устройство:</span>
                        <a href="<?= UserSideHelper::generateUsUrlDevice($item['device_id']) ?>"
                           target="_blank"><?= isset($item['device_name']) ? $item['device_name'] : "device_{$item['device_id']}" ?></a>
                        <?php if (isset($item['device_location'])): ?>
                            <span style="color: #7d7d7d; font-size: small">(<?= $item['device_location'] ?>)</span>
                        <?php endif; ?>
                    </div>
                    <div><span style="font-weight: bold; color: #2c5ba7;">Порт:</span>
                        <a href="<?= UserSideHelper::generateUsUrlPort($item['device_id'], $item['port']) ?>"
                           target="_blank"><?= $item['port'] ?></a>
                        <?php if (isset($item['zte_onu'])): ?>
                            <?= Html::a("<span class='badge badge-primary'>Инфо об ONU</span>", ['/zte/onu/view', 'id' => $item['zte_onu']['onu_id'], 'port' => $item['zte_onu']['onu_port']], ['target' => "_blank", 'style' => '']) ?>
                        <?php endif; ?>
                    </div>


                    <div><span style="font-weight: bold; color: #2c5ba7;">Последняя дата:</span>
                        <?php $date = new Date($item['date_last']); ?>
                        <?php if ($date->getRemainingSecond() > 30 * 60): ?>
                            <span class="badge badge-danger"
                                  title="Дольше 30 минут не была на связи"><?= $date->format(Date::FORMAT_DATE_TIME) ?></span>
                        <?php else: ?>
                            <span class="badge badge-success"
                                  title="Была на связи в течении последних 30 минут"><?= $date->format(Date::FORMAT_DATE_TIME) ?></span>
                        <?php endif; ?>

                        <span style="font-size: small; color: #7d7d7d; font-style: italic">(<?= (new Date($item['date_last']))->toRemainingText(2) ?>)</span>
                    </div>

                    <?php if (isset($item['onu'])): ?>
                        <?php foreach ($item['onu'] as $i => $onu): ?>
                            <div class="mt-2">

                                <?php if (!empty($onu['mac'])): ?>
                                    <div>
                                        <span style="font-weight: bold; color: #009129;">MAC:</span> <?= Internet::toMacNoDelimiter($onu['mac']) ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($onu['sn'])): ?>
                                    <div><span style="font-weight: bold; color: #009129;">SN:</span> <?= $onu['sn'] ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (array_key_exists('iface_state', $onu)): ?>
                                    <div><span style="font-weight: bold; color: #009129;">Состояние:</span>
                                        <span title="iface_state">
                                            <?php if ($onu['iface_state'] == 1): ?>
                                                <i class="far fa-check-circle" style="color: #1cb100"></i>
                                            <?php else: ?>
                                                <i class="far fa-times-circle" style="color: #b11800"></i>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($onu['distance'])): ?>
                                    <div>
                                        <span style="font-weight: bold; color: #009129;">Расстояние до ONU:</span> <?= $onu['distance'] ?>
                                        м.
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($onu['level_onu_rx'])): ?>
                                    <div><span style="font-weight: bold; color: #009129;">Сигнал входящий:</span>
                                        <?= $onu['level_onu_rx'] ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($onu['level_onu_tx'])): ?>
                                    <div>
                                        <span style="font-weight: bold; color: #009129;">Сигнал исходящий:</span> <?= $onu['level_onu_tx'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php //==============================================================?>
            <?php if ($detail->type == Req3IdentifierDetails::TYPE_USER_COUNTERPARTY_CLIENT_SERVICE_PROVIDER): ?>
                <?php
                if ($counterpartyGroup === null) {
                    $userCounterpartyService = Yii::$container->get(UserCounterpartyService::class);
                    $counterpartyGroup = $userCounterpartyService->getGroupCounterpartiesByUserId($value->value_id);
                }
                ?>
                <?php if ($counterpartyGroup->counterpartyClientServiceProvider): ?>
                    <a href="<?= Url::toRoute(['/counterparties/counterparties/view', 'id' => $counterpartyGroup->counterpartyClientServiceProvider->counterpartyId]) ?>"
                       target="_blank">
                        <?= $counterpartyGroup->counterpartyClientServiceProvider->hasCounterpartyInfo()
                            ? $counterpartyGroup->counterpartyClientServiceProvider->getCounterpartyInfo()->getName()
                            : "Не найдено информации"
                        ?>
                    </a>
                <?php else: ?>
                    Заказчик услуги не указан
                <?php endif; ?>
            <?php endif; ?>
            <?php //==============================================================?>
            <?php if ($detail->type == Req3IdentifierDetails::TYPE_USER_COUNTERPARTY_SERVICE_RECIPIENT): ?>
                <?php
                if ($counterpartyGroup === null) {
                    $userCounterpartyService = Yii::$container->get(UserCounterpartyService::class);
                    $counterpartyGroup = $userCounterpartyService->getGroupCounterpartiesByUserId($value->value_id);
                }
                ?>
                <?php if (count($counterpartyGroup->counterpartyServiceRecipients) > 0): ?>
                    <?php $isFirst = 0; ?>
                    <?php foreach ($counterpartyGroup->counterpartyServiceRecipients as $serviceRecipient): ?>
                        <?php if ($isFirst++ > 0) echo ", "; ?>
                        <a href="<?= Url::toRoute(['/counterparties/counterparties/view', 'id' => $serviceRecipient->counterpartyId]) ?>"
                           target="_blank">
                            <?= $serviceRecipient->hasCounterpartyInfo()
                                ? $serviceRecipient->getCounterpartyInfo()->getName()
                                : "Не найдено информации"
                            ?>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    Получатели услуги не указаны
                <?php endif; ?>
            <?php endif; ?>
            <?php //==============================================================?>
            <?php if ($detail->type == Req3IdentifierDetails::TYPE_USER_COUNTERPARTY_SERVICE_PROVIDER): ?>
                <?php
                if ($counterpartyGroup === null) {
                    $userCounterpartyService = Yii::$container->get(UserCounterpartyService::class);
                    $counterpartyGroup = $userCounterpartyService->getGroupCounterpartiesByUserId($value->value_id);
                }
                ?>
                <?php if ($counterpartyGroup->counterpartyServiceProvider): ?>
                    <a href="<?= Url::toRoute(['/counterparties/counterparties/view', 'id' => $counterpartyGroup->counterpartyServiceProvider->counterpartyId]) ?>"
                       target="_blank">
                        <?= $counterpartyGroup->counterpartyServiceProvider->hasCounterpartyInfo()
                            ? $counterpartyGroup->counterpartyServiceProvider->getCounterpartyInfo()->getName()
                            : "Не найдено информации"
                        ?>
                    </a>
                <?php else: ?>
                    Поставщик услуги не указан
                <?php endif; ?>
            <?php endif; ?>
            <?php //==============================================================?>
            <?php if ($detail->type == Req3IdentifierDetails::TYPE_USER_COUNTERPARTY_PROPERTY_OWNER): ?>
                <?php
                if ($counterpartyGroup === null) {
                    $userCounterpartyService = Yii::$container->get(UserCounterpartyService::class);
                    $counterpartyGroup = $userCounterpartyService->getGroupCounterpartiesByUserId($value->value_id);
                }
                ?>
                <?php if ($counterpartyGroup->counterpartyPropertyOwner): ?>
                    <a href="<?= Url::toRoute(['/counterparties/counterparties/view', 'id' => $counterpartyGroup->counterpartyPropertyOwner->counterpartyId]) ?>"
                       target="_blank">
                        <?= $counterpartyGroup->counterpartyPropertyOwner->hasCounterpartyInfo()
                            ? $counterpartyGroup->counterpartyPropertyOwner->getCounterpartyInfo()->getName()
                            : "Не найдено информации"
                        ?>
                    </a>
                <?php else: ?>
                    Владелец помещения не указан
                <?php endif; ?>
            <?php endif; ?>
            <?php //==============================================================?>
            <?php //=====================ТКД======================================?>
            <?php if ($detail->type == Req3IdentifierDetails::TYPE_USER_CAP): ?>
                <?php $userCapAddress = $value->user->cap->address ?? false ?>
                <?= $userCapAddress
                    ? $userCapAddress->getFullName(Locations::TYPE_COUNTRY, true, true)
                    : "Не найдено информации"
                ?>
            <?php endif; ?>
            <?php //==============================================================?>
            <?php if ($detail->type == Req3IdentifierDetails::TYPE_USER_CONNECTION_TYPE): ?>
                <?php $userConnectionType = $value->user->connection_type ?? false ?>
                <?= $userConnectionType ? $value->user->generateConnectionTypeText() : "Не найдено информации" ?>
            <?php endif; ?>
            <?php //==============================================================?>
            <?php if ($detail->type == Req3IdentifierDetails::TYPE_USER_DOWNTIME): ?>
                <?php $downtimeService = Yii::$container->get(ProcessDowntimeService::class); ?>
                <?php $sec = $downtimeService->getByUserId($value->value_id, 30); ?>
                <?= $sec > 0 ? Date::secondsToText($sec, 2) : "Простоев не было" ?>
            <?php endif; ?>
            <?php //==============================================================?>
            <?php if ($detail->type == Req3IdentifierDetails::TYPE_USER_ACS): ?>
                <?php $acsService = Yii::$container->get(AcsService::class); ?>
                <?php $acsEnricher = Yii::$container->get(AcsHouseEnricher::class); ?>
                <?php $acsIds = $acsService->getDeviceIdsByUserIds([$value->value_id]); ?>
                <?php $acs = $acsService->getDeviceByIds($acsIds); ?>
                <?php $acsEnricher->enrichMany($acs); ?>
                <?php if (!empty($acs)): ?>
                    <?= implode(", ", array_map(fn(AcsDeviceDto $it) => $it->getName(), $acs)); ?>
                <?php else: ?>
                    Устройств СКУД не найдено
                <?php endif; ?>
            <?php endif; ?>
            <?php //==============================================================?>
            <?php if ($detail->type == Req3IdentifierDetails::TYPE_USER_KTV): ?>
                <?php $ktvService = Yii::$container->get(KtvService::class); ?>
                <?php $ktvDevices = $ktvService->getDevicesByUserId($value->value_id); ?>
                <?php if (!empty($ktvDevices)): ?>
                    <?= implode(", ", array_map(fn(KtvDeviceDto $it) => $it->name, $ktvDevices)); ?>
                <?php else: ?>
                    Устройств КТВ не найдено
                <?php endif; ?>
            <?php endif; ?>
            <?php //==============================================================?>
        </div>
    <?php endforeach; ?>

    <div style="margin-top: 10px; display: flex; gap: 10px; flex-wrap: wrap">
        <?= $this->render('dop_info', ['task' => $task, 'value' => $value]) ?>
    </div>

<?php else: ?>
    user<?= $value->value_id ?>
<?php endif; ?>


<?php
/** @var RequestUsers[] $crashes */
$crashes = RequestUsers::find()
    ->andWhere([RequestUsers::tableName() . ".user_id" => $value->value_id])
    ->joinWith('crash.reason')
    ->with(['crash'])
    ->andWhere([Request::tableName() . ".status" => [Request::STATUS_WAIT_CONFIRM_EXECUTOR, Request::STATUS_ACTIVE]])
    ->all();
?>


<?php if (count($crashes) > 0): ?>
    <hr>
    <?php foreach ($crashes as $crash): ?>
        <div>
            <a href="<?= Url::toRoute(['/crash/request/view', 'id' => $crash->crash->req_id]) ?>">
                Авария №<?= $crash->crash->req_id ?>
            </a>
            <span style="color: #555555; font-size: small">(<?= $crash->crash->reason->name ?? "" ?>)</span>
        </div>
    <?php endforeach; ?>
<?php endif; ?>