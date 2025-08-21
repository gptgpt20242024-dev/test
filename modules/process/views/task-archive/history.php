<?php
/* @var $this yii\web\View */
/* @var $task app\modules\process\models\task_archive\TaskArchive */
/* @var $items array */
?>

<div data-step-history="1">
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
