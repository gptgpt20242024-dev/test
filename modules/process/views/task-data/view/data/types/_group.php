<?php

use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\Req3TasksData;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $identifier Req3Identifiers */
/* @var $value Req3TasksDataItems */

/* @var $is_editable boolean */
/* @var $is_required boolean */
/* @var $is_only_view boolean */

/* @var $can_edit boolean */

?>

<div class="card card-small mb-0">
    <div class="list-group list-group-small list-group-flush">
        <?php foreach ($identifier->identifier_links as $link): ?>
            <?php if ($link->link_identifier): ?>
                <div class="list-group-item p-0">
                    <?= $this->render('../identifier_sub', [
                        'task'       => $task,
                        'identifier' => $link->link_identifier,
                        'group_data' => $value->children,

                        'is_editable'  => $is_editable,
                        'is_required'  => $is_required,
                        'is_only_view' => $is_only_view,

                        'can_edit'          => $can_edit,
                    ]) ?>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>

