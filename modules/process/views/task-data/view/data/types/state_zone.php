<?php

use app\modules\process\models\task_data\Req3TasksDataItems;
use app\modules\setting\components\Names;
use app\modules\setting\constants\SettingItemType;
use app\modules\setting\constants\SettingStatusZone;
use app\modules\setting\services\SettingService;

/* @var $this yii\web\View */
/* @var $value Req3TasksDataItems */

$zone = false;
$label = false;
$setting = $value->getAddressSetting();
if ($setting) {
    $block = $setting->getMainBlock();
    $zone = $block->getSimpleValue(SettingItemType::STATE_ZONE);
    $label = $block->getSimpleValue(SettingItemType::LABEL);
}

$class = "badge-secondary";
if ($zone === SettingStatusZone::NO) $class = "badge-secondary";
if ($zone === SettingStatusZone::NO_PARTNER) $class = "badge-secondary";
if ($zone === SettingStatusZone::ANALYSIS) $class = "badge-info";
if ($zone === SettingStatusZone::PROCESS) $class = "badge-primary";
if ($zone === SettingStatusZone::LOCALITY_IS_COVERED) $class = "badge-primary";
if ($zone === SettingStatusZone::OK) $class = "badge-success";
if ($zone === SettingStatusZone::NO_FUTURE) $class = "badge-danger";
if ($zone === SettingStatusZone::NO_SELL_TO_PARTNER) $class = "badge-danger";
?>

<div style="float: right; display: flex; flex-direction: column; gap: 5px; align-items: flex-end;">
    <?php if ($zone === false): ?>
        <span class="badge badge-secondary">Не определено</span>
    <?php elseif ($zone === null): ?>
        <span class="badge badge-secondary">Не выставлено</span>
    <?php else: ?>
        <span class="badge <?= $class ?>"><?= SettingStatusZone::NAMES[$zone] ?? "-" ?></span>
    <?php endif; ?>

    <?php if ($label !== null): ?>
        <span class="badge badge-light"><?= Names::getLabelName($label) ?></span>
    <?php endif; ?>
</div>

