<?php

use app\components\Date;
use app\models\Opers;

/* @var $this yii\web\View */
/* @var $task app\modules\process\models\task_archive\TaskArchive */
/* @var $items array */
/* @var $timeExecute array|null */
/* @var $deviationInfo array */
/* @var $timeTemplate int|null */

$color_time_execute_all = "#1f1f1f";
$color_time_active = "#44AC10";
$color_time_deviation = "#CD3333";
$color_time_auto = "#1063AC";
$color_time_not_working = "#8F8F8F";
?>

<div data-step-history="1">
    <div class="card card-small" style="overflow: hidden">
        <div class="card-body">
            <div><span style="color:<?= $color_time_execute_all ?>; font-weight: bold">Всего на задачу ушло:</span> <?= Date::secondsToText($timeExecute['all_time'] ?? 0, 2) ?></div>
            <?php if (($timeExecute['active_time'] ?? 0) > 0): ?>
                <div><span style="color: <?= $color_time_active ?>; font-weight: bold">Активное время:</span> <?= Date::secondsToText($timeExecute['active_time'], 2) ?></div>
            <?php endif; ?>
            <?php if (($timeExecute['deviation_time'] ?? 0) > 0 || ($deviationInfo['all'] ?? 0) > 0): ?>
                <div>
                    <span style="color: <?= $color_time_deviation ?>; font-weight: bold">Отклонения:</span> <?= Date::secondsToText($timeExecute['deviation_time'] ?? 0, 2) ?>
                    <span style="color: #ca2727">(отклонений по задаче: <b><?= $deviationInfo['all'] ?? 0 ?></b> (шаг: <b><?= $deviationInfo['steps'] ?? 0 ?></b> + маршрут: <b><?= $deviationInfo['rules'] ?? 0 ?></b>))</span>
                </div>
            <?php endif; ?>
            <?php if (($timeExecute['auto_time'] ?? 0) > 0): ?>
                <div><span style="color: <?= $color_time_auto ?>; font-weight: bold">Автоматические шаги:</span> <?= Date::secondsToText($timeExecute['auto_time'], 2) ?></div>
            <?php endif; ?>
            <?php if (($timeExecute['not_working_time'] ?? 0) > 0): ?>
                <div><span style="color: <?= $color_time_not_working ?>; font-weight: bold">Не рабочее время:</span> <?= Date::secondsToText($timeExecute['not_working_time'], 2) ?></div>
            <?php endif; ?>
            <?php if (($timeTemplate ?? 0) > 0): ?>
                <div><span style="color: #0023a6; font-weight: bold">На решение задачи даётся активного времени:</span> <?= Date::secondsToText($timeTemplate, 2) ?></div>
            <?php endif; ?>
            <?php if (!empty($timeExecute['opers'])): ?>
                <div data-view-online="1">
                    <div style="color: #aeaeae; font-weight: bold" data-spoiler data-container="[data-view-online]" data-content="[data-spoiler-content-online]">
                        Сколько времени исполнители были в задаче
                        <i class="fas fa-caret-up" data-open="1"></i>
                        <i class="fas fa-caret-down" data-close="1"></i>
                    </div>
                    <div data-spoiler-content-online="1" style="display: none">
                        <?php foreach ($timeExecute['opers'] as $oper_id => $second): ?>
                            <div>
                                <span style="color: #d36148; font-weight: bold;"><?= Opers::getFioOrFioDeletedHtmlById($oper_id) ?></span>: <?= Date::secondsToText($second, 2) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="timeline">
        <?php $last_date = null; ?>
        <?php foreach ($items as $item): ?>
            <?php $date_check = $item['time']; ?>
            <?php require 'history/time_label.php'; ?>

            <?php if ($item['type'] === 'info'): ?>
                <?= $this->render('history/info', ['item' => $item['item']]); ?>
            <?php endif; ?>

            <?php if ($item['type'] === 'link'): ?>
                <?= $this->render('history/link', ['item' => $item['item']]); ?>
            <?php endif; ?>

            <?php if ($item['type'] === 'transition'): ?>
                <?= $this->render('history/transition_info', ['item' => $item['item']]); ?>
            <?php endif; ?>

            <?php if ($item['type'] === 'transition_detail'): ?>
                <?= $this->render('history/transition_detail_info', ['item' => $item['item']]); ?>
            <?php endif; ?>

            <?php if ($item['type'] === 'rule2_detail'): ?>
                <?= $this->render('history/transition_rule2_info', ['task' => $task, 'item' => $item['item']]); ?>
            <?php endif; ?>

            <?php if ($item['type'] === 'step'): ?>
                <?= $this->render('history/step', ['item' => $item['item'], 'online' => $item['online'] ?? []]); ?>
            <?php endif; ?>

            <?php if ($item['type'] === 'function'): ?>
                <?= $this->render('history/function', ['item' => $item['item']]); ?>
            <?php endif; ?>

            <?php if ($item['type'] === 'data'): ?>
                <?= $this->render('history/data_changed', ['item' => $item['item']]); ?>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>
