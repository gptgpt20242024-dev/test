<?php

use app\components\Date;
use app\components\Str;
use app\modules\process\models\task\Req3Tasks;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $item array */

$date = new Date($item['time_start']);
?>

<div style="margin-left: 60px; font-size: small; color: #2343a1" title="Информация">
    <div>
        <i class="fas fa-exclamation-triangle"></i> <?= $date->format(Date::FORMAT_DATE_TIME) ?>
        <span><?= nl2br(Str::toLink($item['message'])) ?></span>
    </div>
</div>