<?php

use app\modules\userside\components\UserSideHelper;
use app\modules\userside\dto\DeviceDto;
use app\modules\zabbix\constants\ZabbixCrashStatus;
use app\modules\zabbix\constants\ZabbixHistoryStatus;
use app\modules\zabbix\constants\ZabbixHistoryType;
use app\modules\zabbix\models\ZabbixCrashQueue;
use app\modules\zabbix\models\ZabbixHistory;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $switchId int */
/* @var $switchPort string */
/* @var $switch ?DeviceDto */
/* @var $items ZabbixHistory[] */
/* @var $crashes ZabbixCrashQueue[] */
?>

<?php if ($switch): ?>
    <div><a href="<?= UserSideHelper::generateUsUrlDevice($switch->id) ?>" target="_blank"><?= $switch->name ?></a></div>
    <?php if (!empty($switch->ip)): ?>
        <div style="color: #298f2e; font-style: italic;"><?= $switch->ip ?></div>
    <?php endif; ?>
    <div style="font-style: italic; font-size: small; color: #5f5f5f"><?= $switch->location ?></div>
<?php else: ?>
    <?= $switchId ?>
<?php endif; ?>

<?php if (!empty($switchPort)): ?>
    <div style="color: #8f5429;"><b>Порт:</b> <?= $switchPort ?></div>
<?php endif; ?>

<?php if (!empty($items)): ?>
    <div class="mt-2" style="color: #298c8f;">Падения:</div>
    <table class="table table-sm table-bordered mb-0">
        <?php foreach ($items as $item): ?>
            <tr>
                <td style="width: 170px"><?= $item->date_start ?></td>
                <td style="width: 170px"><?= $item->date_end ?></td>
                <td><?= ZabbixHistoryType::NAMES[$item->type] ?? "-" ?></td>
                <td><?= ZabbixHistoryStatus::NAMES[$item->status] ?? "-" ?></td>
                <?php if ($item->wait_start_req3 == 1): ?>
                    <td class="table-warning">Ждет запуска БП</td>
                <?php endif; ?>
                <?php if (!empty($item->task_id)): ?>
                    <td>
                        <a href="<?= Url::to(['/process/task/view', 'id' => $item->task_id]) ?>" target="_blank">#<?= $item->task_id ?></a>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<?php if (!empty($crashes)): ?>
    <div class="mt-2" style="color: #8f2929;">Падения устройства:</div>
    <table class="table table-sm table-bordered mb-0">
        <?php foreach ($crashes as $crash): ?>
            <?php
            $data = $crash->getData();
            $code = $data['code'] ?? 99;
            $message = $data['message'] ?? "";
            $crashIds = $data['crashIds'] ?? "";
            $checkDeviceId = $data['checkDeviceId'] ?? -1;
            ?>
            <tr>
                <td style="width: 170px"><?= $crash->date_add ?></td>
                <td><?= ZabbixCrashStatus::NAMES[$crash->status] ?? "-" ?></td>
                <td>
                    <?= $this->render('@app/modules/zabbix/views/report/_code', ['code' => $code]) ?>
                    <div style="color: #727272; font-size: small"><?= $message ?></div>
                </td>
                <td>
                    <?php if (!empty($crashIds)): ?>
                        <?php foreach ($crashIds as $crashId): ?>
                            <div>
                                <a href="<?= Url::to(['/crash/request/view', 'id' => $crashId]) ?>" target="_blank">
                                    #<?= $crashId ?>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
