<?php

use app\modules\crash\models\Request;
use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItems;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $identifier Req3Identifiers */
/* @var $value Req3TasksDataItems */

/* @var $is_editable boolean */
/* @var $is_required boolean */
/* @var $is_only_view boolean */

/* @var $can_edit boolean */

?>


<?php if (!$value->crash): ?>
    <div class="px-2 py-2">
        <div class="alert alert-default-warning mb-0">
            Аварии нет.
            <?php if ($is_editable && !$is_only_view && $can_edit && $task): ?>
                <div>
                    <a class="btn btn-sm btn-success text-white text-decoration-none" href="<?= Url::toRoute(['/crash/create/step1', 'req_id' => $task->id]) ?>" target="_blank">Создать</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <ul class="list-group list-group-flush">
        <li class="list-group-item px-3 py-2">
            <?php if ($value->crash): ?>
                <?php if ($value->crash->status == Request::STATUS_CREATED): ?>
                    Авария в процессе создания
                    <div>
                        <a class="btn btn-sm btn-primary text-white text-decoration-none" href="<?= Url::toRoute(['/crash/create/step1', 'crash_id' => $value->value_id]) ?>" target="_blank">Продолжить</a>
                    </div>
                <?php else: ?>
                    <?= Html::a("Авария №" . $value->value_id, ["/crash/request/view", 'id' => $value->value_id], ['target' => '_blank']) ?>
                    <?= $this->render('@app/modules/crash/views/request/_info', ['crash' => $value->crash]) ?>
                <?php endif; ?>
            <?php else: ?>
                Авария id_<?= $value->value_id ?>
            <?php endif; ?>
        </li>
    </ul>
<?php endif; ?>