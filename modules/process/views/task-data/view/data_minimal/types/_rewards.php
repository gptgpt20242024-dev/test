<?php

use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $value Req3TasksDataItems */
?>

<?= $value->reward->work_rater->name ?? "reward_$value->value_id" ?>

