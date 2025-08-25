<?php

use app\modules\process\models\task\Req3Tasks;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
?>

<div style="display: flex; flex-wrap: wrap; gap: 10px">
    <?php foreach ($task->started_orders as $linkOrder): ?>
        <?= $this->render('sub_order', ['task' => $task, 'linkOrder' => $linkOrder]); ?>
    <?php endforeach; ?>
</div>
