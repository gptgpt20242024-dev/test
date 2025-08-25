<?php

use app\components\Date;
use app\models\Opers;
use app\modules\process\models\task\Req3Tasks;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
?>

<?php if (!$task->hired_oper_id): ?>
    <button type="button" class="btn btn-outline-light btn-sm text-left" title="Отметить что я занимаюсь этой задачей, не обязательно, несет информационный характер" data-toggle="tooltip" onclick="setHired(<?= $task->id ?>)">
        <span style="color: #a1a1a1"><i class="fas fa-user-lock"></i> Взять в работу</span>
        <div style="font-size: 11px; color: #cecece;">Просто для информации</div>
    </button>
<?php else: ?>
    <div class="btn-group" role="group" aria-label="Basic example">
        <button type="button" class="btn btn-success btn-sm text-left" disabled style="border: 1px solid #008d1a !important; background-color: #07a803">
            <i class="fas fa-exclamation-triangle" style="color: #ff0202; text-shadow: 0 -1px 4px #FFF, 0 -2px 10px #ff0, 0 -10px 20px #ff8000, 0 -18px 40px #F00;"></i>
        </button>
        <button type="button" class="btn btn-outline-success btn-sm text-left" style="border: 1px solid #008d1a !important;" <?= !$task->isAccessUnsetHired(Yii::$app->user->identity) ? "disabled" : "" ?> title="Этот человек отметил что он занимается этой задачей, несет информационный характер" data-toggle="tooltip" onclick="unsetHired(<?= $task->id ?>)">
            <span style=""><i class="fas fa-user-lock"></i> В работе: <?= Opers::getFioOrFioDeletedHtml($task, 'hired_oper', 'hired_oper_id') ?></span>
            <div style="font-size: 11px; color: #c2d5b3;">Просто для информации (<?= (new Date($task->date_when_hired))->toRemainingText(1, true) ?>)</div>
        </button>
    </div>
<?php endif; ?>