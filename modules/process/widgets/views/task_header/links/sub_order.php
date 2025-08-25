<?php

use app\components\Date;
use app\modules\order\models\Task;
use app\modules\process\components\HelperOper;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task\Req3TaskStartedOrders;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $linkOrder Req3TaskStartedOrders */

?>

<div class="card mb-0" style="overflow: hidden; display: inline-block; vertical-align: top; width: 400px; max-width: 100%">
    <div class="card-body px-3 py-2">
        <?php if ($linkOrder->order): ?>
            <div class="float-right">
                <?php
                $has_move = false;
                foreach ($linkOrder->order->work_links as $work_link) {
                    if (count($work_link->moves) > 0) {
                        $has_move = true;
                        break;
                    }
                }
                ?>

                <?php if ($linkOrder->order->status == Task::STATUS_ACTIVE) echo "<i style='color: #1270c8' class='fas fa-lightbulb' title='Активно'></i>"; ?>
                <?php if ($linkOrder->order->status == Task::STATUS_PENDING_VERIFICATION) echo "<i style='color: #ded100' class='fas fa-lightbulb' title='Ожидает проверки ОТК'></i>"; ?>
                <?php if ($linkOrder->order->status == Task::STATUS_COMPLETE) echo "<i style='color: #00c831' class='fas fa-lightbulb' title='Выполнено'></i>"; ?>
                <?php if ($has_move) echo "<i style='color: #c3c3c3' class='fas fa-warehouse' title='Были списания'></i>"; ?>
            </div>
        <?php endif; ?>

        <a href="<?= Url::toRoute(['/order/task/view', 'id' => $linkOrder->order_id]) ?>" target="_blank">
            <div style="white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
                Наряд #<?= $linkOrder->order_id ?>
            </div>
        </a>
    </div>
    <div class="card-footer px-3 py-2" style="font-size: small">
        <span style="color: #595959"><?= HelperOper::getFio($linkOrder, 'oper_id', 'oper') ?></span>
        <div>
            <?php $date = new Date($linkOrder->date_start) ?>
            <?= $date->format(Date::FORMAT_DATE_TIME) ?> <span style="color: #6b6b6b">(<?= $date->toRemainingText(2, true) ?>)</span>
        </div>
    </div>
</div>