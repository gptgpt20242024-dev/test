<?php

use app\components\Str;
use app\modules\process\models\task\Req3Tasks;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
?>

<?php if ($task->active_chat): ?>
    <button type="button" class="btn btn-outline-info btn-sm shadow-sm text-left" title="Активный чат" data-toggle="tooltip" onclick="showDialogChat(<?= $task->active_chat->id ?>)">
        <i class="fab fa-rocketchat"></i> <b>Активный чат:</b> <?= Str::compactOverflow($task->active_chat->topic, 20) ?>
        <div style="font-size: 11px; color: #9f9f9f;">Участников: <?= count($task->active_chat->getActiveMembers()) ?></div>
    </button>
<?php endif; ?>