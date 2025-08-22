<?php

use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $value Req3TasksDataItems */
?>

<?= $value->reward_bonus_item->name ?? "reward_bonus_item_{$value->value_id}" ?>