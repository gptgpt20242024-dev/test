<?php

use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItems;
use app\modules\process\models\task_data\Req3TasksDataItemSteps;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $identifier Req3Identifiers */
/* @var $value Req3TasksDataItems */

/* @var $is_editable boolean */
/* @var $is_required boolean */
/* @var $is_only_view boolean */

/* @var $can_edit boolean */


$tree = Req3TasksDataItemSteps::createTree($value->template_steps, $task);
$priority = $value->value_number;
if ($priority !== null) {
    if ($priority > 1000) $priority = round($priority / 1000, 0) . "k";
    else {
        $priority = round($priority / 1000, 1);
    }
}
?>

<ul>
    <?php foreach ($tree as $item): ?>
        <?= $this->render('_template_steps_item', ['item' => $item]) ?>
    <?php endforeach; ?>
</ul>
<?php if ($priority !== null): ?>
    <?= $priority ?>
<?php endif; ?>
