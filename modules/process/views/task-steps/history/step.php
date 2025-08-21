<?php

use app\components\Date;
use app\models\Opers;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task\Req3TasksStepHistory;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $item Req3TasksStepHistory */
/* @var $online [] */

$date = new Date($item->start_date);
$end_date = new Date($item->end_date);

$label = empty($item->end_date) ? $task->queue_label : $item->queue_label;
?>

<div>
    <?php if ($item->step && $item->step->is_first): ?>
        <i class="fas fa-play" style="background-color: #2443ff; color: #fafafa;"></i>
    <?php elseif ($item->step && $item->step->is_auto): ?>
        <i class="fas fa-robot" style="background-color: #54d5d2; color: #fafafa;"></i>
    <?php elseif ($item->step && $item->step->is_calls): ?>
        <i class="fas fa-phone" style="background-color: #d5b154; color: #fafafa;"></i>
    <?php elseif ($item->step && $item->step->isDeviation()): ?>
        <i class="fas fa-exclamation-circle" style="background-color: #d55454; color: #fafafa;"></i>
    <?php elseif ($item->step && $item->step->is_last): ?>
        <i class="fas fa-step-forward" style="background-color: #5e9925; color: #fafafa;"></i>
    <?php else: ?>
        <i class="fas fa-shoe-prints" style="background-color: #c5c5c5; color: #fafafa;"></i>
    <?php endif; ?>

    <div class="timeline-item clearfix" style="display: flex; gap: 10px; flex-wrap: wrap">

        <div class="timeline-body" style="flex-grow: 1; flex-basis: min-content;">
            <div style="font-weight: bold; min-width: 200px;">
                <a href="<?= Url::toRoute(['/process/step/view', 'id' => $item->step_id]) ?>" target="_blank">
                    <?= $item->step->name ?? ("Шаг удален (id " . $item->step_id . ")") ?>
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


        <span class="time" style="margin-left: auto; white-space: nowrap">
            <div>
                <i class="fas fa-clock"></i>
                <?php $sec = $date->subtractDateTime($end_date) ?>
                <?php $title = "время сколько были на шаге (пришел: " . $date->format(Date::FORMAT_DATE_TIME) . ($item->end_date != null ? (", ушел: " . $end_date->format(Date::FORMAT_DATE_TIME)) : "") . ")"; ?>
                <span title="<?= $title ?>"><?= Date::secondsToText($sec, 2) ?></span>
            </div>

            <?php if ($label): ?>
                <span class="badge badge-light" style="background-color: <?= $label->color ?>"><?= $label->label ?></span>
            <?php endif; ?>

            <?php if ($item->escalation > 0): ?>
                <div class="text-danger">
                    <i class="fas fa-meh"></i>
                    <span>Эскалация: <?= $item->escalation ?></span>
                </div>
            <?php endif; ?>

            <?php if ((!empty($item->end_date) && $item->is_overdue) || (empty($item->end_date) && $task->isOverdueStep())): ?>
                <div class="text-danger">
                    <i class="fas fa-meh"></i>
                    <span>Просрочен</span>
                </div>
            <?php endif; ?>

            <?php if ($item->step && $item->step->isDeviation()): ?>
                <?php if (!$item->isDeviationJobComplete()): ?>
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