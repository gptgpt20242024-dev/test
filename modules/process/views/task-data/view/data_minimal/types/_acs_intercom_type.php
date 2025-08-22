<?php

use app\modules\acs\constants\IntercomType;
use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $value Req3TasksDataItems */

?>

<?= IntercomType::getName($value->value_id); ?>

