<?php

use app\components\Date;
use app\models\Opers;
use app\modules\process\models\task\Req3Tasks;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $items array */

$next_show_id = 1;
$show_id = null;
$show_spoiler = false;

$time_execute = $task->getTimeExecute();
$color_time_execute_all = "#1f1f1f";
$color_time_active = "#44AC10";
$color_time_deviation = "#CD3333";
$color_time_auto = "#1063AC";
$color_time_not_working = "#8F8F8F";

$time_template = ($task->version->execute_minutes ?? 0) * 60;
$deviation_info = $task->getDeviationInfo();
?>

<div data-step-history="1">

    <div class="card card-small" style="overflow: hidden">
        <div class="card-body">
            <div><span style="color:<?= $color_time_execute_all ?>; font-weight: bold">Всего на задачу ушло:</span> <?= Date::secondsToText($time_execute['all_time'], 2) ?></div>
            <?php if ($time_execute['active_time'] > 0): ?>
                <div><span style="color: <?= $color_time_active ?>; font-weight: bold">Активное время:</span> <?= Date::secondsToText($time_execute['active_time'], 2) ?></div>
            <?php endif; ?>
            <?php if ($time_execute['deviation_time'] > 0 || $deviation_info['all'] > 0): ?>
                <div>
                    <span style="color: <?= $color_time_deviation ?>; font-weight: bold">Отклонения:</span> <?= Date::secondsToText($time_execute['deviation_time'], 2) ?>
                    <span style="color: #ca2727">(отклонений по задаче: <b><?= $deviation_info['all'] ?></b> (шаг: <b><?= $deviation_info['steps'] ?></b> + маршрут: <b><?= $deviation_info['rules'] ?></b>))
                </div>
            <?php endif; ?>
            <?php if ($time_execute['auto_time'] > 0): ?>
                <div><span style="color: <?= $color_time_auto ?>; font-weight: bold">Автоматические шаги:</span> <?= Date::secondsToText($time_execute['auto_time'], 2) ?></div>
            <?php endif; ?>
            <?php if ($time_execute['not_working_time'] > 0): ?>
                <div><span style="color: <?= $color_time_not_working ?>; font-weight: bold">Не рабочее время:</span> <?= Date::secondsToText($time_execute['not_working_time'], 2) ?></div>
            <?php endif; ?>
            <?php if ($time_template > 0): ?>
                <div><span style="color: #0023a6; font-weight: bold">На решение задачи даётся активного времени:</span> <?= Date::secondsToText($time_template, 2) ?></div>
            <?php endif; ?>

            <?php if (count($time_execute['opers']) > 0): ?>
                <div data-view-online="1">
                    <div style="color: #aeaeae; font-weight: bold" data-spoiler data-container="[data-view-online]" data-content="[data-spoiler-content-online]">
                        Сколько времени исполнители были в задаче
                        <i class="fas fa-caret-up" data-open="1"></i>
                        <i class="fas fa-caret-down" data-close="1"></i>
                    </div>
                    <div data-spoiler-content-online="1" style="display: none">
                        <?php foreach ($time_execute['opers'] as $oper_id => $second): ?>
                            <div>
                                <span style="color: #d36148; font-weight: bold;"><?= Opers::getFioOrFioDeletedHtmlById($oper_id) ?></span>: <?= Date::secondsToText($second, 2) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>

        <?= $this->render('/task/view/progress/time_life', ['task' => $task]) ?>
        <?= $this->render('/task/view/progress/time_template', ['task' => $task]) ?>
    </div>


    <div class="timeline">
        <?php $last_date = null; ?>
        <?php foreach ($items as $item): ?>

            <?php $date_check = $item['time']; ?>
            <?php require 'history/time_label.php'; ?>

            <?php if ($item['type'] == 'info'): ?>
                <?php $show_id = null; ?>
                <?= $this->render('history/info', ['item' => $item['item']]); ?>
            <?php endif; ?>

            <?php if ($item['type'] == 'link'): ?>
                <?php $show_id = null; ?>
                <?= $this->render('history/link', ['item' => $item['item']]); ?>
            <?php endif; ?>

            <?php if ($item['type'] == 'transition'): ?>
                <?php $show_id = null; ?>
                <?= $this->render('history/transition_info', ['item' => $item['item']]); ?>
            <?php endif; ?>

            <?php if ($item['type'] == 'transition_detail'): ?>
                <?php $show_id = null; ?>
                <?= $this->render('history/transition_detail_info', ['task' => $task, 'item' => $item['item']]); ?>
            <?php endif; ?>

            <?php if ($item['type'] == 'rule2_detail'): ?>
                <?php $show_id = null; ?>
                <?= $this->render('history/transition_rule2_info', ['task' => $task, 'item' => $item['item']]); ?>
            <?php endif; ?>

            <?php if ($item['type'] == 'step'): ?>
                <?php $show_id = null; ?>
                <?= $this->render('history/step', ['task' => $task, 'item' => $item['item'], 'online' => $item['online'] ?? []]); ?>
            <?php endif; ?>


            <?php if ($item['type'] == 'function'): ?>
                <?= $this->render('history/function', ['item' => $item['item']]); ?>
            <?php endif; ?>

            <?php if ($item['type'] == 'data'): ?>

                <?php if ($show_id === null): ?>
                    <?php $next_show_id++; ?>
                    <?php $show_id = $next_show_id; ?>
                    <div style="margin-left: 60px; font-size: small; color: #9b9b9b;" data-spoiler data-container=".timeline" data-content="[data-spoiler-content=<?= $show_id ?>]">
                        <div>
                            <i class="fas fa-expand-alt mr-3" style="color: #9a9a9a;" data-close="1"></i>
                            <i class="fas fa-compress-alt mr-3" style="color: #9a9a9a; display: none" data-open="1"></i>

                            Детализация
                        </div>
                    </div>

                <?php endif; ?>

                <?= $this->render('history/data_changed', ['item' => $item['item'], 'show_id' => $show_id]); ?>
            <?php endif; ?>


        <?php endforeach; ?>

    </div>

    <script>
        $(function () {
            $('[data-toggle="tooltip"]').bootstraptooltip();
        });
    </script>
</div>