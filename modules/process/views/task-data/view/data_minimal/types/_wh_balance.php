<?php

use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $identifier Req3Identifiers */
/* @var $value Req3TasksDataItems */

$isNeedItems = $identifier->getSettingByKey(Req3Identifiers::SETTING_WH_NEED_ITEMS, false);
?>


<?php if ($value->balance): ?>
    <?php $info = $value->balance->generateCurrentBalance(); ?>
    <?= $info['type'] ?>: <?= $info['value1'] ?>
    <?php if ($isNeedItems && count($value->balanceItems) > 0): ?>
        <ul>
            <?php foreach ($value->balanceItems as $balanceItem): ?>
                <li>
                    <?= $balanceItem->whItem->name ?? "-" ?> (<?= $balanceItem->wh_count ?>)
                    <?php if ($balanceItem->wh_uitem_id != -1 && !empty($balanceItem->whUniqueItem->serial ?? null)): ?>
                        [<?= $balanceItem->whUniqueItem->serial ?>]
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
<?php else: ?>
    balance<?= $value->value_id ?>
<?php endif; ?>


