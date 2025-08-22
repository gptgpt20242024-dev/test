<?php

use app\modules\process\models\Req3TasksData;
use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $value Req3TasksDataItems */
?>

<?= number_format($value->value_number, 2, ',', ' ') ?>
