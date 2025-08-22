<?php

use app\modules\address\models\Locations;
use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItems;
use app\modules\utm\models\BlocksInfo;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $identifier Req3Identifiers */
/* @var $value Req3TasksDataItems */

?>

<?php if ($value->user): ?>
    ЛС: <?= $value->user->utm_acc_id ?>
    <div><?= $value->user->getAddressFullName(Locations::TYPE_COUNTRY, true, true) ?></div>

    <div>
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
    </div>

    <div>
        <?php if ($value->user->utm_account): ?>
            <span style="color: #012fb4;"><?= number_format($value->user->utm_account->balance, 2, ',', ' ') . "руб." ?></span>
            <?php if ($value->user->utm_account->block_info && $value->user->utm_account->block_info->block_type == BlocksInfo::BLOCK_TYPE_ADMIN): ?>
                <?php if ($value->user->adm_block_current): ?>
                    <span style="color: #b41200;">(<span style="color: #ab4126;">До блокировки:</span><?= number_format($value->user->adm_block_current->prev_balance, 2, ',', ' ') . "руб." ?>)</span>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>


<?php else: ?>
    user<?= $value->value_id ?>
<?php endif; ?>

