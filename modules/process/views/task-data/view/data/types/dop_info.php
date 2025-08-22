<?php

use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItems;
use app\modules\setting\assets\AddressSettingAsset;
use app\modules\setting\constants\SettingItemType;
use app\modules\setting\constants\SettingLinkType;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $value Req3TasksDataItems */

AddressSettingAsset::register($this);
list($linkId, $linkType) = $value->getAddressSettingId();

$data = [];
if ($linkType != SettingLinkType::USER) {
    $data[SettingItemType::FM] = $task->fm_id;
    foreach ($task->data as $item) {
        if ($item->type == Req3Identifiers::TYPE_USER_TYPE) {
            $data[SettingItemType::USER_TYPE] = $item->value_id;
        }
    }
}
?>

<div class="btn-group">
    <button type="button" class="btn btn-warning btn-sm" onclick='showDopInfo(<?= $linkId ?>, <?= $linkType ?>, <?= json_encode($data) ?>)'>Доп. инфо</button>

    <button type="button" class="btn btn-warning btn-sm dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="sr-only">Toggle Dropdown</span>
    </button>
    <div class="dropdown-menu">
        <a class="dropdown-item" href="#" onclick='showDopInfo(<?= $linkId ?>, <?= $linkType ?>, <?= json_encode($data) ?>, <?= json_encode([
            SettingItemType::TARIFFS,
            SettingItemType::TARIFFS_ADMIN,
            SettingItemType::SERVICES,
            SettingItemType::SERVICES_SERVICE,
            SettingItemType::COMPETITORS,
            SettingItemType::OFFERS,
            SettingItemType::COST_CONNECT,
            SettingItemType::COST_CONNECT_SERVICE,
            SettingItemType::COST_CONNECT_KTV,
            SettingItemType::COST_CONNECT_KTV_SERVICE,
            SettingItemType::COST_CONNECT_KTV_EXIST,
            SettingItemType::COST_CONNECT_KTV_EXIST_SERVICE,
            SettingItemType::COST_RECONNECT,
            SettingItemType::COST_RECONNECT_SERVICE,
            SettingItemType::PERIOD_CONNECT_DAYS,
            SettingItemType::STATE_ZONE,
            SettingItemType::TEXT
        ]) ?>)'>
            Подключение
        </a>
    </div>
</div>