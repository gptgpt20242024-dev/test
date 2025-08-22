<?php

use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataRemarks;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $identifier Req3Identifiers */
/* @var $remarks Req3TasksDataRemarks[] */
?>

<div class="js_remarks">
    <?php if (count($remarks) > 0): ?>
        <div class="card-footer px-2 py-2">
            <div style="display: flex; gap: 10px; flex-direction: column">
                <?php foreach ($remarks as $remark): ?>
                    <?= $this->render('remark', [
                        'task'       => $task,
                        'identifier' => $identifier,
                        'remark'     => $remark,
                        'only_view'  => false,
                    ]) ?>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
