<?php

use app\assets\AudioPlayer;
use app\modules\process\components\HelperGetCalls;
use app\modules\process\models\calls\Req3CallsStatuses;
use app\modules\process\models\task\Req3Tasks;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */

AudioPlayer::register($this);
?>


<?php foreach ($task->calls_statuses_step as $status): ?>
    <?php if ($status->reason == Req3CallsStatuses::REASON_NO_NUMBERS): ?>
        <i><?= $status->getStatusName() ?></i> - <?= $status->getReasonName() ?>
    <?php endif; ?>


    <?php foreach ($status->status_phones as $status_phone): ?>
        <?php if ($status_phone->count_calls > 1): ?>
            <span style="color: #296789">(Попытка <?= $status_phone->count_calls ?>/<?= HelperGetCalls::COUNT_CALL ?>)</span>
        <?php endif; ?>

        <i><?= $status_phone->getStatusName() ?></i>
    <?php endforeach; ?>
<?php endforeach; ?>