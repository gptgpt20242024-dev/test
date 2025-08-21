<?php

use app\components\Date;
use app\models\Opers;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $item array */
/* @var $online array */

$date = new Date($item['start_date']);
$end_date = new Date($item['end_date']);
$label = $item['label'] ?? null;
?>

<div>

    <?php if ($item['step_is_first']??0): ?>
        <i class="fas fa-play" style="background-color: #2443ff; color: #fafafa;"></i>
    <?php elseif ($item['step_is_auto']??0): ?>
        <i class="fas fa-robot" style="background-color: #54d5d2; color: #fafafa;"></i>
    <?php elseif ($item['step_is_calls']??0): ?>
        <i class="fas fa-phone" style="background-color: #d5b154; color: #fafafa;"></i>
    <?php elseif ($item['step_is_deviation']??0): ?>
        <i class="fas fa-exclamation-circle" style="background-color: #d55454; color: #fafafa;"></i>
    <?php elseif ($item['step_is_last']??0): ?>
        <i class="fas fa-step-forward" style="background-color: #5e9925; color: #fafafa;"></i>
    <?php else: ?>
        <i class="fas fa-shoe-prints" style="background-color: #c5c5c5; color: #fafafa;"></i>
    <?php endif; ?>

    <div class="timeline-item clearfix" style="display: flex; gap: 10px; flex-wrap: wrap">
        <div class="timeline-body" style="flex-grow:1; flex-basis: min-content;">
            <div style="font-weight:bold; min-width:200px;">
                <a href="<?= Url::toRoute(['/process/step/view', 'id' => ($item['step_id']??0)]) ?>" target="_blank">
                    <?= $item['step_name'] ?? ('Шаг ' . $item['step_id']) ?>
                </a>
            </div>
            <?php if (count($online) > 0): ?>
                <div data-view-online="1" style="font-size: small;">
                    <div style="color: #aeaeae" data-spoiler data-container="[data-view-online]" data-content="[data-spoiler-content-online]">
                        Сколько времени исполнители были на шаге
                        <i class="fas fa-caret-up" data-open="1"></i>
                        <i class="fas fa-caret-down" data-close="1"></i>
                    </div>
                    <div data-spoiler-content-online="1" style="display: none">
                        <?php foreach ($online as $oper_id => $second): ?>
                            <div>
                                <span style="color: #d36148; font-weight: bold;"><?= Opers::getFioOrFioDeletedHtmlById($oper_id) ?></span>: <?= Date::secondsToText($second, 2) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <span class="time" style="margin-left:auto; white-space:nowrap">
            <div>
                <i class="fas fa-clock"></i>
                <?php $sec = $date->subtractDateTime($end_date); ?>
                <span><?= Date::secondsToText($sec, 2) ?></span>
            </div>
            <?php if ($label): ?>
                <span class="badge badge-light" style="background-color: <?= $label['color'] ?>"><?= $label['label'] ?></span>
            <?php endif; ?>

            <?php if (($item['escalation']??0) > 0): ?>
                <div class="text-danger">
                    <i class="fas fa-meh"></i>
                    <span>Эскалация: <?= $item['escalation'] ?></span>
                </div>
            <?php endif; ?>

            <?php if ((!empty($item['end_date']) && $item['is_overdue'])): ?>
                <div class="text-danger">
                    <i class="fas fa-meh"></i>
                    <span>Просрочен</span>
                </div>
            <?php endif; ?>

            <?php if ($item['step_is_deviation']??0): ?>
                <?php if (!($item['is_deviation_job_complete']??0)): ?>
                    <div style="color: #317728">
                    <i class="fas fa-check-double" style="color: #00c625"></i> Работа выполнена
                </div>
                <?php else: ?>
                    <div style="color: #a9a9a9">
                    <i class="fas fa-times"></i> Работа не выполнена
                </div>
                <?php endif; ?>
            <?php endif; ?>

        </span>
    </div>
</div>
