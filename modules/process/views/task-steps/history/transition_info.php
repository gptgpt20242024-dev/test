<?php

use app\components\Date;
use app\modules\process\components\HelperOper;
use app\modules\process\models\task\Req3TasksStepHistory;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $item Req3TasksStepHistory */

$date = new Date($item->start_date);
?>


    <div style="margin-left: 60px; font-size: small; color: #9b9b9b" title="Информация о переходе">
        <div>
            <i class="fas fa-clock"></i> <?= $date->format(Date::FORMAT_DATE_TIME) ?>
            <span style="color: #9985af">(<?= HelperOper::getFio($item, 'oper_id', 'oper') ?>)</span>
        </div>

        <?php if ($item->transition_id != null): ?>
            <div>
                <i class="fas fa-mouse"></i>
                Задача двинута по старой системе переходов
            </div>
        <?php endif; ?>
    </div>


<?php if ($item->from_task_id !== null): ?>
    <div>
        <i class="fas fa-directions" style="background-color: #458eb5; color: #fafafa;"></i>

        <div class="timeline-item clearfix">
            <div class="timeline-body">
                БП запущен из другого БП:
                <?php if ($item->from_task): ?>
                    <?= Html::a($item->from_task->name, ['/process/task/view', 'id' => $item->from_task_id], ['target' => "_blank"]) ?>
                <?php else: ?>
                    <span style="color: #9a1d1b; text-decoration-line: underline">Родительский БП удален</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>