<?php

use app\components\Date;
use app\modules\process\components\HelperOper;
use app\modules\process\models\task\Req3Tasks;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $item array */

$date = new Date($item['time_start']);


?>

<div style="margin-left: 60px; font-size: small; color: #2343a1" title="Информация">
    <div>

        <?php if ($item['type'] == "link"): ?>
            <i class="fas fa-link" style="color: #048712"></i>
            <span style="color: #048712">(<?= HelperOper::getFioById($item['oper_id']) ?>)</span>
        <?php endif; ?>

        <?php if ($item['type'] == "unlink"): ?>
            <i class="fas fa-unlink" style="color: #870404"></i>
            <span style="color: #870404">(<?= HelperOper::getFioById($item['oper_id']) ?>)</span>
        <?php endif; ?>

        <span style="color: #999999"><?= $date->format(Date::FORMAT_DATE_TIME) ?></span>

        <?php if ($item['type'] == "link"): ?>
            Привязана
        <?php endif; ?>

        <?php if ($item['type'] == "unlink"): ?>
            Отвязана
        <?php endif; ?>

        <?php if (isset($item['child_id'])): ?>
            дочерняя задача: <?= Html::a(Req3Tasks::getNameDeletedHtml($item['child_id']), ['/process/task/view', 'id' => $item['child_id']], ['target' => "_blank"]) ?>
        <?php endif; ?>

        <?php if (isset($item['parent_id'])): ?>
            родительская задача: <?= Html::a(Req3Tasks::getNameDeletedHtml($item['parent_id']), ['/process/task/view', 'id' => $item['parent_id']], ['target' => "_blank"]) ?>
        <?php endif; ?>

    </div>
</div>