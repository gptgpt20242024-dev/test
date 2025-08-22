<?php

use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItems;
use app\modules\warehouse\constants\BalanceLinkTypes;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $identifier Req3Identifiers */
/* @var $value Req3TasksDataItems */

/* @var $is_editable boolean */
/* @var $is_required boolean */
/* @var $is_only_view boolean */

/* @var $can_edit boolean */

$isNeedItems = $identifier->getSettingByKey(Req3Identifiers::SETTING_WH_NEED_ITEMS, false);
?>

<?php if ($value->balance): ?>

    <div style="font-size: small; color: #858585">
        <?php $info = $value->balance->generateCurrentBalance(); ?>

        <?php if ($info['type_id'] == BalanceLinkTypes::USER): ?>
            <i class="fas fa-user"></i>
        <?php elseif ($info['type_id'] == BalanceLinkTypes::TECH): ?>
            <i class="fas fa-user-cog"></i>
        <?php elseif ($info['type_id'] == BalanceLinkTypes::WAREHOUSE): ?>
            <i class="fas fa-warehouse"></i>
        <?php elseif ($info['type_id'] == BalanceLinkTypes::CAP): ?>
            <i class="fas fa-server"></i>
        <?php endif; ?>

        <?= $info['type'] ?>
    </div>
    <div><?= $info['value1'] ?></div>

    <?php if ($isNeedItems): ?>
        <div class="card card-small mt-2 mb-1" style="background-color: #f3f3f3">
            <ul class="list-group list-group-small">
                <?php foreach ($value->balanceItems as $balanceItem): ?>
                    <li class="list-group-item" style="background-color: inherit; display: flex; gap: 10px; align-items: baseline;">

                        <div class="badge badge-light">
                            <?= $balanceItem->wh_count ?>
                        </div>

                        <div>
                            <?= $balanceItem->whItem->name ?? "-" ?>
                            <?php if ($balanceItem->wh_uitem_id != -1 && !empty($balanceItem->whUniqueItem->serial ?? null)): ?>
                                <div style="color: #0d3285; font-weight: bold; font-size: small; font-family: Courier, monospace">
                                    <?= $balanceItem->whUniqueItem->serial ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

<?php else: ?>
    balance<?= $value->value_id ?>
<?php endif; ?>
