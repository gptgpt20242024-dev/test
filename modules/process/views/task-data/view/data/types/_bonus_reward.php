<?php

use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\rewards\Req3RewardItems;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $identifier Req3Identifiers */
/* @var $value Req3TasksDataItems */

/* @var $is_editable boolean */
/* @var $is_required boolean */
/* @var $is_only_view boolean */

/* @var $can_edit boolean */

?>

<?php if ($value->reward_bonus_item): ?>

    <div style="font-weight: bold; font-size: small">
        <?php if ($value->reward_bonus_item->type == Req3RewardItems::TYPE_REWARD): ?>
            <span style="color: #a80002">Штраф</span>
        <?php else: ?>
            <span style="color: #00a816">Бонус</span>
        <?php endif; ?>

    </div>

    <?= $value->reward_bonus_item->name ?>

    <div style="font-weight: bold; font-size: small">
        <?php if ($value->reward_bonus_item->type == Req3RewardItems::TYPE_REWARD): ?>
            <span style="color: #a80002"><?= $value->reward_bonus_item->value ?><?= ($value->reward_bonus_item->value_type == Req3RewardItems::VALUE_TYPE_PERCENT) ? "%" : "р." ?></span>
        <?php else: ?>
            <span style="color: #00a816"><?= $value->reward_bonus_item->value ?><?= ($value->reward_bonus_item->value_type == Req3RewardItems::VALUE_TYPE_PERCENT) ? "%" : "р." ?></span>
        <?php endif; ?>
    </div>

<?php else: ?>

<?php endif; ?>
