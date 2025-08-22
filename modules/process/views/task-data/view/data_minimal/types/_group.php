<?php

use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\Req3TasksData;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $identifier Req3Identifiers */
/* @var $value Req3TasksDataItems */
?>

<div class="ml-3">
    <?php foreach ($identifier->identifier_links as $link): ?>
        <?php if ($link->link_identifier): ?>
            <div>
                <?= $this->render('../identifier_sub', ['task' => $task, 'identifier' => $link->link_identifier, 'task_data' => $value->children]) ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>