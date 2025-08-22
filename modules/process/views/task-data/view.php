<?php

use app\modules\process\dto\data\TaskDataDto;
use app\modules\process\dto\RuleDataDto;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\widgets\TaskDataWidget;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $ruleData RuleDataDto */
/* @var $taskData TaskDataDto */

?>

<?= TaskDataWidget::widget([
    'task'     => $task,
    'ruleData' => $ruleData,
    'taskData' => $taskData,
]) ?>
