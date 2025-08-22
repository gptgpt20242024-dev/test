<?php

use app\modules\process\models\identifiers\Req3IdentifierOrders;
use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */

$identifiers = [];
if ($task->step) {
    foreach ($task->step->views as $view) {
        if ($view->is_display_in_main && $view->identifier) {
            $identifiers[$view->identifier_id] = $view->identifier;
        }
    }
}
Req3IdentifierOrders::sort($task->version_id, $identifiers);
?>
<div data-minimal="1">

    <?php foreach ($identifiers as $identifier): ?>
        <div>
            <?= $this->render('data_minimal/identifier', ['task' => $task, 'identifier' => $identifier]) ?>
        </div>
<?php endforeach; ?>


    <?php foreach ($task->step->views as $view): ?>
        <?php if ($view->is_display_in_main && !empty($view->temp_type)): ?>
            <div>
                <?= $this->render('@app/modules/process/views/identifiers/_icon_type', ['type' => $view->temp_type]) ?>
                <b>
                    <?php if ($view->temp_type == Req3Identifiers::TYPE_TEMP_EXECUTOR) echo "Исполнитель"; ?>
                    <?php if ($view->temp_type == Req3Identifiers::TYPE_TEMP_CONTROLLER) echo "Ответственный"; ?>
                    <?php if ($view->temp_type == Req3Identifiers::TYPE_TEMP_OBSERVER) echo "Наблюдатель"; ?>
                    :</b>
                <?= $this->render('data_minimal/type', ['task' => $task, 'identifier' => null, 'value' => new Req3TasksDataItems(['type' => $view->temp_type])]) ?>
            </div>
        <?php endif; ?>
<?php endforeach; ?>

</div>
