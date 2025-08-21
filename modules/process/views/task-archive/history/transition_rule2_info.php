<?php

use app\components\Date;
use app\modules\process\components\HelperOper;
use app\modules\process\models\task\Req3Tasks;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $item array */

$date = new Date($item['time_start']);
?>

<div style="margin-left: 60px; font-size: small; color: #9b9b9b" title="Детальная информация о переходе">
    <div>
        <i class="fas fa-clock"></i> <?= $date->format(Date::FORMAT_DATE_TIME) ?>
        <span style="color: #9985af">(<?= HelperOper::getFioById($item['oper_id']) ?>)</span>
    </div>

    <?php if ($item['rule2_id']): ?>
        <div>
            <i class="fas fa-mouse" style="padding: 0 2px"></i>
            Двинул задачу далее по маршруту #<?= $item['rule2_id'] ?>
            <a href="#" onclick="showDialogStepRulesInfo(<?= $item['from_step_id'] ?>, <?= $task->id ?>, <?= $item['rule2_id'] ?>, <?= json_encode($item['triggeredRuleIds'] ?? []) ?>)">
                (инфо)
            </a>
        </div>
    <?php endif; ?>

    <?php if (isset($item['ok']) && !$item['ok']): ?>
        <div style="color: #d18353;">
            <i class="fas fa-exclamation-triangle"></i>
            переход не удался
        </div>
    <?php endif; ?>

</div>