<?php

use app\components\Date;
use app\modules\process\components\HelperOper;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $item array */

?>

<div style="margin-left: 60px; font-size: small; color: #9b9b9b;">

    <?php if ($item['time'] ?? false): ?>
        <div><i class="fas fa-clock"></i> <?= (new Date($item['time']))->format(Date::FORMAT_DATE_TIME) ?>

            <?php if ($item['oper_id'] ?? false): ?>
                <span style="color: #9985af">(<?= HelperOper::getFioById($item['oper_id']) ?>)</span>
            <?php endif; ?>
        </div>
    <?php endif; ?>


    <div style="overflow: hidden; word-break: break-word;"><i class="fas fa-pencil-alt"></i> <b style="color: #6f8ea9;"><?= $item['name'] ?></b>:
        <?php if (count($item['value'] ?? []) == 0): ?>
            <i class="fas fa-trash-alt"></i> Очищено
        <?php else: ?>
            <?= nl2br(Html::encode(implode(", ", $item['value']))) ?>
        <?php endif; ?>
    </div>
</div>
