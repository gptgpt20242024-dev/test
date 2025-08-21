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

    <?php if (isset($item['transition'])): ?>
        <div>
            <i class="fas fa-mouse" style="padding: 0 2px"></i>
            Нажал на кнопку: <span style="color: #279eb9;"><?= $item['transition']['name'] ?></span> (старая система переходов)
        </div>
    <?php endif; ?>

    <?php if (isset($item['ok']) && !$item['ok']): ?>
        <div style="color: #d18353;">
            <i class="fas fa-exclamation-triangle"></i>
            переход не удался
        </div>
    <?php endif; ?>

</div>