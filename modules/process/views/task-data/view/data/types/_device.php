<?php

use app\components\Date;
use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItems;
use app\modules\userside\constants\ObjectType;
use app\modules\userside\services\CommutationService;
use app\modules\userside\services\DeviceService;
use app\modules\zabbix\models\ZabbixCrashQueue;
use app\modules\zabbix\models\ZabbixHistory;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $identifier Req3Identifiers */
/* @var $value Req3TasksDataItems */

/* @var $is_editable boolean */
/* @var $is_required boolean */
/* @var $is_only_view boolean */

/* @var $can_edit boolean */

$deviceService = Yii::$container->get(DeviceService::class);
$switch = $deviceService->getById($value->value_id);
$allIds = [$value->value_id => $value->value_id];
$ringDeviceIds = [];
$ports = [];
$commutations = [];
$devices = [];

if ($switch) {
    $commutationService = Yii::$container->get(CommutationService::class);
    $commutations = $commutationService->getById($value->value_id, ObjectType::DEVICE, [ObjectType::DEVICE], true);
    $ports = $switch->getUpDnLink();

    foreach ($ports as $position => $port) {
        foreach ($commutations[$value->value_id][$position] ?? [] as $deviceId => $commutation) {
            $ringDeviceIds[$deviceId] = $deviceId;
            $allIds[$deviceId] = $deviceId;
        }
    }
    $devices = $deviceService->getByIds($ringDeviceIds);
}

$date = (new Date($task->create_date))->subtractMinutes(5)->format();

/** @var ZabbixHistory[] $items */
$items = ZabbixHistory::find()
    ->andWhere(['device_id' => $allIds])
    ->andWhere([
        'OR',
        ['>=', 'date_start', $date],
        ['>=', 'date_end', $date],
    ])
    ->all();
$items = ArrayHelper::index($items, null, 'device_id');

$crash = ZabbixCrashQueue::find()
    ->andWhere(['device_id' => $allIds])
    ->andWhere([
        'OR',
        ['>=', 'date_add', $date],
        ['>=', 'date_add', $date],
    ])
    ->all();
$crash = ArrayHelper::index($crash, null, 'device_id');



?>

<?= $this->render('device/device', [
    'switchId'   => $value->value_id,
    'switchPort' => $value->value_text,
    'switch'     => $switch,
    'items'      => $items[$value->value_id] ?? [],
    'crashes'    => $crash[$value->value_id] ?? [],
]) ?>

<?php if (!empty($ringDeviceIds)): ?>
    <div class="card mt-3">
        <ul data-rings class="list-group mb-0">
            <li class="list-group-item d-flex justify-content-between align-items-center" style="background-color: #fff3ea" data-spoiler data-container="[data-rings]">
                <div>
                    <i class="fas fa-expand-alt text-secondary" data-close="1"></i>
                    <i class="fas fa-compress-alt text-secondary" data-open="1" style="display: none"></i>
                    Кольца:
                </div>
                <span class="badge badge-primary badge-pill"><?= count($ringDeviceIds) ?></span>
            </li>
            <div data-spoiler-content style="display: none">
                <?php foreach ($ports as $position => $port): ?>
                    <?php foreach ($commutations[$value->value_id][$position] ?? [] as $toDeviceId => $commutationPositions): ?>
                        <?php foreach ($commutationPositions as $toPosition => $commutation): ?>
                            <?php $toDevice = $devices[$toDeviceId] ?? null; ?>
                            <?php $toIface = $toDevice ? ($toDevice->ifaces[$toPosition] ?? null) : null; ?>

                            <li class="list-group-item" style="background-color: #fffbf7">
                                С порта <span class="badge badge-warning"><?= $port->name ?></span> в порт <span class="badge badge-warning"><?= $toIface->name ?? "position $toPosition" ?></span>
                                <div class="card card-small mt-1">
                                    <div class="card-body">

                                        <?= $this->render('device/device', [
                                            'switchId'   => $toDeviceId,
                                            'switchPort' => null,
                                            'switch'     => $toDevice,
                                            'items'      => $items[$toDeviceId] ?? [],
                                            'crashes'    => $crash[$toDeviceId] ?? [],
                                        ]) ?>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
        </ul>
    </div>
<?php endif; ?>
