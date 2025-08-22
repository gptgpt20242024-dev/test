<?php

use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $identifier Req3Identifiers */
/* @var $task Req3Tasks */
/* @var $task_data Req3TasksDataItems */

$is_multi = $identifier->is_multi;

$values = [];
foreach ($task_data as $value) {
    if ($value->identifier_id == $identifier->id && $value->type == $identifier->type) {
        $values[] = $value;
        if (!$is_multi) break;
    }
}

?>

<?= $this->render('@app/modules/process/views/identifiers/_icon_type', ['type' => $identifier->type]) ?>
    <i><?= $identifier->name ?>:</i>

<?php if (count($values) > 0): ?>
    <?php $i = 0; ?>
    <?php foreach ($values as $value): ?>
        <?php if ($i++ > 0) echo ", " ?>
        <?= $this->render('type', ['task' => $task, 'identifier' => $identifier, 'value' => $value]) ?>
    <?php endforeach; ?>
<?php else: ?>
    <span style="color: #808080">Данные не заполнены</span>
<?php endif; ?>